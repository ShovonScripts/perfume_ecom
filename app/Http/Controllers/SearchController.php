<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live search products (JSON API).
     */
    public function index(Request $request)
    {
        $q = trim($request->q);

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $products = Product::with('brand')
            ->activeWithVariants()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhereHas('brand', function ($b) use ($q) {
                          $b->where('name', 'like', "%{$q}%");
                      });
            })
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'slug'      => $product->slug,
                    'brand'     => $product->brand->name,
                    'thumbnail' => $product->thumbnail_image
                        ? asset('storage/' . $product->thumbnail_image)
                        : null,
                    'price'     => $product->variants()->where('is_active', true)->min('price'),
                ];
            });

        return response()->json($products);
    }
}
