<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this product
        if (ProductReview::where('user_id', Auth::id())->where('product_id', $product->id)->exists()) {
        // Optional: Allow updating existing review or fail
        // For now, let's just update it if it exists (or fail if unique constraint catches it, but we should handle it gracefully)
        }

        ProductReview::updateOrCreate(
        [
            'product_id' => $product->id,
            'user_id' => Auth::id(),
        ],
        [
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'is_approved' => false, // Always require re-approval on update
        ]
        );

        return back()->with('success', 'Review submitted successfully. It will be visible after approval.');
    }
}
