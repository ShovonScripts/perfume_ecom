<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->withCount('products')
            ->with(['children' => function ($q) {
            $q->withCount('products')->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('category.index', compact('categories'));
    }

    /**
     * Display products in a category with filtering.
     */
    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get child category IDs too (so parent category shows all children's products)
        $categoryIds = collect([$category->id]);
        $childIds = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->pluck('id');
        $categoryIds = $categoryIds->merge($childIds);

        $query = Product::with(['brand', 'images', 'variants' => function ($q) {
            $q->where('is_active', true);
        }])
            ->whereIn('category_id', $categoryIds)
            ->activeWithVariants();

        // Brand Filter
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', $request->brands);
        }

        // Price Filter (min)
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('is_active', true)
                    ->where('price', '>=', $request->min_price);
            });
        }

        // Price Filter (max)
        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('is_active', true)
                    ->where('price', '<=', $request->max_price);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');

        if ($sort === 'price_low') {
            $query->addSelect(['min_price' => \App\Models\ProductVariant::selectRaw('MIN(price)')
                ->whereColumn('product_id', 'products.id')
                ->where('is_active', true)
            ])->orderBy('min_price', 'asc');
        }
        elseif ($sort === 'price_high') {
            $query->addSelect(['min_price' => \App\Models\ProductVariant::selectRaw('MIN(price)')
                ->whereColumn('product_id', 'products.id')
                ->where('is_active', true)
            ])->orderBy('min_price', 'desc');
        }
        elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        }
        elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        }
        else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Brand::where('is_active', true)
            ->whereHas('products', function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds)
                ->where('is_active', true);
        })
            ->get();

        // Get subcategories for navigation
        $subcategories = Category::where('parent_id', $category->id)
            ->where('is_active', true)
            ->get();

        $metaTitle = $category->name . ' - ' . config('app.name');
        $metaDescription = $category->description ?? 'Browse ' . $category->name . ' collection. Best prices, authentic products.';

        return view('category.show', compact(
            'category', 'products', 'brands', 'subcategories', 'metaTitle', 'metaDescription'
        ));
    }
}
