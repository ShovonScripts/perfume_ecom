<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function sitemap()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        return response()->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        return response(
            "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /dashboard\nSitemap: " . url('/sitemap.xml'),
            200,
        ['Content-Type' => 'text/plain']
        );
    }

    public function orderSuccess(\App\Models\Order $order)
    {
        return view('order.success', compact('order'));
    }
}
