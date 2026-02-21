<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products (Shop Page).
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants' => function ($q) {
            $q->where('is_active', true);
        }])->activeWithVariants();

        // 1. Filter by Category
        if ($request->has('category')) {
            $slugs = explode(',', $request->category);
            $query->whereHas('category', function ($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            });
        }

        // 2. Filter by Brand
        if ($request->has('brand')) {
            $slugs = explode(',', $request->brand);
            $query->whereHas('brand', function ($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            });
        }

        // 3. Filter by Price Range
        if ($request->has('min_price') && $request->has('max_price')) {
            $min = $request->min_price;
            $max = $request->max_price;
            $query->whereHas('variants', function ($q) use ($min, $max) {
                $q->whereBetween('price', [$min, $max]);
            });
        }

        // 4. Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                // complex sort by min variant price
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->select('products.*', \Illuminate\Support\Facades\DB::raw('MIN(product_variants.price) as min_price'))
                    ->groupBy('products.id')
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->select('products.*', \Illuminate\Support\Facades\DB::raw('MIN(product_variants.price) as min_price'))
                    ->groupBy('products.id')
                    ->orderBy('min_price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default: // newest
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Sidebar Data
        $categories = \App\Models\Category::withCount('products')->orderBy('name')->get();
        $brands = \App\Models\Brand::where('is_active', true)->orderBy('name')->get();

        return view('shop.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Display the product detail page.
     */
    public function show($slug)
    {
        $product = Product::with([
            'brand',
            'category',
            'variants' => function ($q) {
            $q->where('is_active', true);
        },
            'images'
        ])
            ->where('slug', $slug)
            ->activeWithVariants()
            ->firstOrFail();

        $siteName = \App\Models\Setting::get('site_name', config('app.name'));
        $metaTitle = "Buy {$product->name} in Bangladesh | Best Price | {$siteName}";
        $metaDescription = $product->meta_description ?? $product->short_description ?? '';
        $metaImage = $product->thumbnail_image;
        $ogType = 'product';

        return view('product.show', compact(
            'product', 'metaTitle', 'metaDescription', 'metaImage', 'ogType'
        ));
    }
}
