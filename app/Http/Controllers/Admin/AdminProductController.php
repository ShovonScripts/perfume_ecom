<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['brand', 'category', 'variants'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.create', compact('brands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'thumbnail_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
        ]);

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail_image')) {
            $thumbnailPath = $request->file('thumbnail_image')
                ->store('products', 'public');
        }

        $product = Product::create([
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'thumbnail_image' => $thumbnailPath,
            'is_active' => $request->has('is_active'),
        ]);

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }

            // Handle Variants
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'ml_value' => $variantData['ml_value'],
                        'ml_unit' => $variantData['ml_unit'] ?? 'ml',
                        'type' => $variantData['type'] ?? null,
                        'price' => $variantData['price'],
                        'compare_price' => $variantData['compare_price'] ?? null,
                        'stock' => $variantData['stock'],
                        'sku' => $variantData['sku'],
                        'sort_order' => $variantData['sort_order'] ?? 0,
                        'is_active' => isset($variantData['is_active']),
                    ]);
                }
            }
        }

        // Bust homepage caches
        Cache::forget('home.new_arrivals');
        Cache::forget('home.best_sellers');

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::all();
        $categories = Category::all();
        $product->load(['images', 'variants']);

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'thumbnail_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'variants' => 'array',
            'variants.*.sku' => 'required|distinct', // We will handle DB uniqueness manually or with more complex rule if needed, but for now let's trust the logic below. 
            // Better: loop and validate manually if we want precise "ignore" rules, or use closures.
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_image')) {
            // Delete old thumbnail
            if ($product->thumbnail_image) {
                Storage::disk('public')->delete($product->thumbnail_image);
            }
            $product->thumbnail_image = $request->file('thumbnail_image')
                ->store('products', 'public');
        }

        // Handle remove thumbnail
        if ($request->has('remove_thumbnail') && $product->thumbnail_image) {
            Storage::disk('public')->delete($product->thumbnail_image);
            $product->thumbnail_image = null;
        }

        try {
            $product->update([
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'thumbnail_image' => $product->thumbnail_image,
                'is_active' => $request->has('is_active'),
            ]);

            // Handle gallery image deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id === $product->id) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Handle new gallery images
            if ($request->hasFile('gallery_images')) {
                $maxSort = $product->images()->max('sort_order') ?? 0;
                foreach ($request->file('gallery_images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $maxSort + $index + 1,
                    ]);
                }
            }

            // Handle Variants: Sync (Delete removed, Update existing, Create new)
            $currentVariantIds = $product->variants()->pluck('id')->toArray();
            $submittedVariantIds = [];

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    if (isset($variantData['id'])) {
                        $submittedVariantIds[] = $variantData['id'];
                    }
                }
            }

            // Delete variants missing from request
            $toDelete = array_diff($currentVariantIds, $submittedVariantIds);
            ProductVariant::whereIn('id', $toDelete)->delete();

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    if (isset($variantData['id'])) {
                        // Update existing by ID
                        $variant = ProductVariant::find($variantData['id']);
                    }
                    else {
                        // Try to find by SKU + Product ID to prevent duplicates if ID was lost
                        $variant = ProductVariant::where('product_id', $product->id)
                            ->where('sku', $variantData['sku'])
                            ->first();
                    }

                    if ($variant) {
                        $variant->update([
                            'ml_value' => $variantData['ml_value'],
                            'ml_unit' => $variantData['ml_unit'] ?? 'ml',
                            'type' => $variantData['type'] ?? null,
                            'price' => $variantData['price'],
                            'compare_price' => $variantData['compare_price'] ?? null,
                            'stock' => $variantData['stock'],
                            'sku' => $variantData['sku'],
                            'sort_order' => $variantData['sort_order'] ?? null,
                            'is_active' => isset($variantData['is_active']),
                        ]);
                    }
                    else {
                        // Create new
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'ml_value' => $variantData['ml_value'],
                            'ml_unit' => $variantData['ml_unit'] ?? 'ml',
                            'type' => $variantData['type'] ?? null,
                            'price' => $variantData['price'],
                            'compare_price' => $variantData['compare_price'] ?? null,
                            'stock' => $variantData['stock'],
                            'sku' => $variantData['sku'],
                            'sort_order' => $variantData['sort_order'] ?? null,
                            'is_active' => isset($variantData['is_active']),
                        ]);
                    }
                }
            }

            // Bust homepage caches
            Cache::forget('home.new_arrivals');
            Cache::forget('home.best_sellers');

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');

        }
        catch (\Exception $e) {
            return back()->with('error', 'Error updating product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete thumbnail
        if ($product->thumbnail_image) {
            Storage::disk('public')->delete($product->thumbnail_image);
        }

        // Delete gallery images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        // Bust homepage caches
        Cache::forget('home.new_arrivals');
        Cache::forget('home.best_sellers');

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
