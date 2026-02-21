<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    public function index()
    {
        $items = $this->cart->getItems();
        $subtotal = $this->cart->subtotal();
        $discount = $this->cart->discount();
        $comboDiscount = $this->cart->comboDiscount();
        $total = $this->cart->total();
        $coupon = $this->cart->getCoupon();

        return view('cart.index', compact('items', 'subtotal', 'discount', 'comboDiscount', 'total', 'coupon'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->cart->add(
                $request->variant_id,
                $request->quantity
            );
        }
        catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')
            ->with('success', 'Product added to cart!');
    }

    public function update(Request $request)
    {
        $this->cart->update(
            $request->variant_id,
            $request->quantity
        );

        return redirect()->route('cart.index')
            ->with('success', 'Cart updated!');
    }

    public function remove(Request $request)
    {
        $this->cart->remove($request->variant_id);

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->isValid($this->cart->subtotal())) {
            return back()->withErrors(['coupon' => 'Invalid or expired coupon code']);
        }

        $this->cart->applyCoupon($coupon);

        return back()->with('success', 'Coupon applied successfully');
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();
        return back()->with('success', 'Coupon removed successfully');
    }
}
