<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminHeroBannerController extends Controller
{
    public function index()
    {
        $banners = HeroBanner::orderBy('sort_order')->get();
        return view('admin.hero-banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.hero-banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_path' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('image_path')->store('banners', 'public');

        HeroBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $path,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.hero-banners.index')
            ->with('success', 'Banner created successfully!');
    }

    public function edit(HeroBanner $heroBanner)
    {
        return view('admin.hero-banners.edit', ['banner' => $heroBanner]);
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image_path')) {
            // Delete old image
            if ($heroBanner->image_path) {
                Storage::disk('public')->delete($heroBanner->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('banners', 'public');
        }

        $heroBanner->update($data);

        return redirect()->route('admin.hero-banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    public function destroy(HeroBanner $heroBanner)
    {
        if ($heroBanner->image_path) {
            Storage::disk('public')->delete($heroBanner->image_path);
        }

        $heroBanner->delete();

        return redirect()->route('admin.hero-banners.index')
            ->with('success', 'Banner deleted successfully!');
    }
}
