<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function addToCart(\App\Models\Combo $combo)
    {
        if (!$combo->is_active) {
            return back()->with('error', 'This combo is currently unavailable.');
        }

        $cartService = app(\App\Services\CartService::class);

        foreach ($combo->items as $item) {
            // Check stock logic handles inside CartService typically, 
            // but we add it blind as per standard instructions.
            $cartService->add(
                $item->product_variant_id,
                $item->quantity
            );
        }

        // To make sure combo discount applies, if we use separate items, we might need a coupon or special cart item.
        // The user said: 
        // 1️⃣ Each product আলাদা লাইন আইটেম হিসেবে যাবে? (Recommended)
        // 2️⃣ Combo as single cart item হিসেবে যাবে?
        // Let's add them as separate line items for now as recommended.

        return redirect()->route('cart.index')->with('success', 'Combo added to cart!');
    }
}
