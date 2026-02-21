<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class AdminProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = ProductReview::with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductReview $review)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $review->update([
            'is_approved' => $request->is_approved,
        ]);

        return back()->with('success', 'Review status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
