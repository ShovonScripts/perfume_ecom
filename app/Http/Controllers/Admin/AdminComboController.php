<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $combos = \App\Models\Combo::orderBy('sort_order')->get();
        return view('admin.combos.index', compact('combos'));
    }

    public function create()
    {
        $products = \App\Models\Product::with('variants')->where('is_active', true)->get();
        return view('admin.combos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:combos,slug',
            'combo_price' => 'required|numeric|min:0',
            'variants' => 'required|array',
            'variants.*' => 'exists:product_variants,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $data = $request->except(['image', 'variants']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('combos', 'public');
        }

        $combo = \App\Models\Combo::create($data);

        foreach ($request->variants as $variantId) {
            \App\Models\ComboItem::create([
                'combo_id' => $combo->id,
                'product_variant_id' => $variantId,
                'quantity' => 1
            ]);
        }

        return redirect()->route('admin.combos.index')->with('success', 'Combo created successfully.');
    }

    public function show(string $id)
    {
    // Not used
    }

    public function edit(string $id)
    {
        $combo = \App\Models\Combo::with('items')->findOrFail($id);
        $products = \App\Models\Product::with('variants')->where('is_active', true)->get();
        $selectedVariants = $combo->items()->pluck('product_variant_id')->toArray();
        return view('admin.combos.edit', compact('combo', 'products', 'selectedVariants'));
    }

    public function update(Request $request, string $id)
    {
        $combo = \App\Models\Combo::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:combos,slug,' . $combo->id,
            'combo_price' => 'required|numeric|min:0',
            'variants' => 'required|array',
            'variants.*' => 'exists:product_variants,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $data = $request->except(['image', 'variants']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($combo->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($combo->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($combo->image);
            }
            $data['image'] = $request->file('image')->store('combos', 'public');
        }

        $combo->update($data);

        $combo->items()->delete();
        foreach ($request->variants as $variantId) {
            \App\Models\ComboItem::create([
                'combo_id' => $combo->id,
                'product_variant_id' => $variantId,
                'quantity' => 1
            ]);
        }

        return redirect()->route('admin.combos.index')->with('success', 'Combo updated successfully.');
    }

    public function destroy(string $id)
    {
        $combo = \App\Models\Combo::findOrFail($id);
        if ($combo->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($combo->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($combo->image);
        }
        $combo->delete();

        return redirect()->route('admin.combos.index')->with('success', 'Combo deleted successfully.');
    }
}
