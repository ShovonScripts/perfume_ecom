<?php

namespace App\Services;

use App\Models\ProductVariant;

class CartService
{
    protected $sessionKey = 'cart.items';
    protected $couponKey = 'cart.coupon';

    public function getItems()
    {
        return session()->get($this->sessionKey, []);
    }

    public function add($variantId, $quantity = 1)
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        if (!$variant->is_active || !$variant->product->is_active) {
            throw new \Exception('This product is currently unavailable.');
        }

        $items = $this->getItems();

        $key = $variantId;

        if (isset($items[$key])) {
            $items[$key]['quantity'] += $quantity;
            $items[$key]['line_total'] =
                $items[$key]['quantity'] * $items[$key]['unit_price'];
        }
        else {

            $items[$key] = [
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'name' => $variant->product->name,
                'ml_value' => $variant->ml_value,
                'ml_unit' => $variant->ml_unit,
                'unit_price' => $variant->price,
                'quantity' => $quantity,
                'line_total' => $variant->price * $quantity,
                'image' => $variant->product->thumbnail_image,
            ];
        }

        session()->put($this->sessionKey, $items);
    }

    public function update($variantId, $quantity)
    {
        $items = $this->getItems();

        if (isset($items[$variantId])) {
            $items[$variantId]['quantity'] = $quantity;
            $items[$variantId]['line_total'] =
                $quantity * $items[$variantId]['unit_price'];
        }

        session()->put($this->sessionKey, $items);
    }

    public function remove($variantId)
    {
        $items = $this->getItems();

        unset($items[$variantId]);

        session()->put($this->sessionKey, $items);
    }

    public function clear()
    {
        session()->forget($this->sessionKey);
    }

    public function subtotal()
    {
        return collect($this->getItems())
            ->sum('line_total');
    }

    public function applyCoupon($coupon)
    {
        session()->put($this->couponKey, $coupon->code);
    }

    public function getCoupon()
    {
        $code = session()->get($this->couponKey);
        if (!$code)
            return null;

        return \App\Models\Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    public function removeCoupon()
    {
        session()->forget($this->couponKey);
    }

    public function discount()
    {
        $coupon = $this->getCoupon();
        $subtotal = $this->subtotal();

        if (!$coupon || !$coupon->isValid($subtotal)) {
            return 0;
        }

        return $coupon->calculateDiscount($subtotal);
    }

    public function comboDiscount()
    {
        $cartItems = collect($this->getItems());

        $combos = \App\Models\Combo::with('items.variant')
            ->where('is_active', true)
            ->get();

        $totalDiscount = 0;

        foreach ($combos as $combo) {
            $match = true;

            foreach ($combo->items as $item) {
                $cartItem = $cartItems
                    ->firstWhere('product_variant_id', $item->product_variant_id);

                if (!$cartItem || $cartItem['quantity'] < $item->quantity) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                // If the combo is matched, determine how many times the combo can be applied
                $comboInstances = PHP_INT_MAX;
                foreach ($combo->items as $item) {
                    $cartItem = $cartItems->firstWhere('product_variant_id', $item->product_variant_id);
                    $possibleTimes = floor($cartItem['quantity'] / $item->quantity);
                    $comboInstances = min($comboInstances, $possibleTimes);
                }

                $discount = $combo->original_total - $combo->combo_price;

                if ($discount > 0 && $comboInstances > 0) {
                    $totalDiscount += ($discount * $comboInstances);
                }
            }
        }

        return $totalDiscount;
    }

    public function total($shippingFee = 0, $discount = 0)
    {
        // If discount param is passed from outside, meaning coupon discount.
        // We'll also use internal discount method if not passed.
        if ($discount == 0) {
            $discount = $this->discount();
        }

        $comboDiscount = $this->comboDiscount();

        return max(0, $this->subtotal() - $discount - $comboDiscount + $shippingFee);
    }
}
