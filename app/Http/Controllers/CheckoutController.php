<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Mail\OrderPlacedMail;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    protected $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function show()
    {
        $items = $this->cart->getItems();

        if (empty($items)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $subtotal = $this->cart->subtotal();
        $discount = $this->cart->discount();
        $comboDiscount = $this->cart->comboDiscount();
        $total = $this->cart->total();
        $coupon = $this->cart->getCoupon();

        $insideFee = Setting::get('shipping_inside_dhaka_fee', 70);
        $outsideFee = Setting::get('shipping_outside_dhaka_fee', 130);

        $districts = [
            'Dhaka', // Dhaka is special, handled separately in logic but kept here for list or separated below
            'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogura', 'Brahmanbaria', 'Chandpur', 'Chattogram', 'Chuadanga', 'Comilla', "Cox's Bazar", 'Dinajpur', 'Faridpur', 'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore', 'Jhalokati', 'Jhenaidah', 'Joypurhat', 'Khagrachari', 'Khulna', 'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur', 'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh', 'Naogaon', 'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Netrokona', 'Nilphamari', 'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi', 'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon'
        ];

        // Remove Dhaka from array if exists to ensure it's not duplicated when we prepend/handle it manually
        $districts = array_diff($districts, ['Dhaka']);
        sort($districts);
        // Prepend Dhaka
        array_unshift($districts, 'Dhaka');

        return view('checkout.index', compact('items', 'subtotal', 'discount', 'comboDiscount', 'total', 'coupon', 'insideFee', 'outsideFee', 'districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'address_line' => 'required|string|max:500',
        ]);

        $items = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();

        if (empty($items)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        // Shipping Logic
        $shippingZone = strtolower($request->city) === 'dhaka'
            ? 'inside_dhaka'
            : 'outside_dhaka';

        $insideFee = Setting::get('shipping_inside_dhaka_fee', 70);
        $outsideFee = Setting::get('shipping_outside_dhaka_fee', 130);

        $shippingFee = $shippingZone === 'inside_dhaka'
            ? $insideFee
            : $outsideFee;

        $discount = $this->cart->discount();
        $comboDiscount = $this->cart->comboDiscount();
        $totalDiscount = $discount + $comboDiscount;
        $grandTotal = $subtotal - $totalDiscount + $shippingFee;

        // BEGIN TRANSACTION 🔥
        DB::beginTransaction();

        try {
            // STOCK CHECK FIRST 🔥
            foreach ($items as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);

                if (!$variant || $variant->stock < $item['quantity']) {
                    throw new \Exception('Insufficient stock for ' . $item['name']);
                }
            }

            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'area' => $request->area,
                'address_line' => $request->address_line,
                'shipping_zone' => $shippingZone,
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'discount' => $totalDiscount,
                'grand_total' => $grandTotal,
                'delivery_charge_prepaid' => $shippingZone === 'outside_dhaka',
            ]);

            // Create Order Items + Decrement Stock
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'name_snapshot' => $item['name'],
                    'ml_value' => $item['ml_value'],
                    'ml_unit' => $item['ml_unit'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);

                // STOCK DECREMENT 🔥
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant) {
                    $variant->decrement('stock', $item['quantity']);
                }
            }

            // COMMIT TRANSACTION 🔥
            DB::commit();

            // Bust dashboard caches
            Cache::forget('admin.dashboard.stats');
            Cache::forget('admin.dashboard.chart');
            Cache::forget('admin.dashboard.top_products');

            // SEND ORDER CONFIRMATION EMAIL 📧 (Queued)
            if ($order->email) {
                try {
                    Mail::to($order->email)->queue(new OrderPlacedMail($order));
                }
                catch (\Exception $e) {
                    // Email failure should not break checkout
                    \Log::warning('Order confirmation email failed: ' . $e->getMessage());
                }
            }

            // Clear Cart
            $this->cart->clear();

            return redirect()->route('orders.payment', $order);

        }
        catch (\Exception $e) {
            // ROLLBACK ON ERROR 🔥
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
