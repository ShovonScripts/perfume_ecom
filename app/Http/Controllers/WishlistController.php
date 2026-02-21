<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with('product.brand', 'product.images')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('items'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ])->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Removed from wishlist';
        }
        else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id
            ]);
            $message = 'Added to wishlist';
        }

        return back()->with('success', $message);
    }
}
