<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\HeroBanner;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Get active banners
        $heroBanners = HeroBanner::active()->get();

        // 1. Categories - limit to active ones
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        // 2. New Arrivals (latest 8 products)
        $newArrivals = Product::with(['brand', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        // 3. Best Sellers (most ordered products)
        // This is a slightly heavier query, so we cache it or optimize.
        // For now, simple approach: count order_items for each product
        $bestSellers = Product::with(['brand', 'variants'])
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(6)
            ->get();

        // 4. Featured Brands (with logos)
        $brands = \App\Models\Brand::whereNotNull('logo')->where('is_active', true)->take(12)->get();
        // Fallback if no logos yet, just get active brands
        if ($brands->count() < 4) {
            $brands = \App\Models\Brand::where('is_active', true)->take(12)->get();
        }

        // 5. Combos
        $combos = \App\Models\Combo::with('items.variant.product')->where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        // SEO
        $metaTitle = "Premium Perfumes Online in Bangladesh";
        $metaDescription = "Shop 100% authentic branded perfumes at best prices. Fast delivery in Dhaka & all over Bangladesh.";

        return view('home', compact(
            'heroBanners',
            'categories',
            'newArrivals',
            'bestSellers',
            'brands',
            'combos',
            'metaTitle',
            'metaDescription'
        ));
    }
}
