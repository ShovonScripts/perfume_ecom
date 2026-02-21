<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'inside' => Setting::get('shipping_inside_dhaka_fee'),
            'outside' => Setting::get('shipping_outside_dhaka_fee'),
            'site_name' => Setting::get('site_name', 'Perfume Store'),
            'site_logo' => Setting::get('site_logo'),
            'site_favicon' => Setting::get('site_favicon'),
            'footer_description' => Setting::get('footer_description'),
            'facebook_url' => Setting::get('facebook_url'),
            'instagram_url' => Setting::get('instagram_url'),
            'youtube_url' => Setting::get('youtube_url'),
            'support_phone' => Setting::get('support_phone'),
            'support_email' => Setting::get('support_email'),
            'bkash_number' => Setting::get('bkash_number'),
            'nagad_number' => Setting::get('nagad_number'),
            'bkash_qr' => Setting::get('bkash_qr'),
            'nagad_qr' => Setting::get('nagad_qr'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'inside' => 'required|numeric|min:0',
            'outside' => 'required|numeric|min:0',
            'site_name' => 'nullable|string|max:255',
            'footer_description' => 'nullable|string|max:1000',
            'facebook_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'support_phone' => 'nullable|string|max:30',
            'support_email' => 'nullable|email|max:255',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,ico,svg|max:512',
            'bkash_number' => 'nullable|string|max:20',
            'nagad_number' => 'nullable|string|max:20',
            'bkash_qr' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'nagad_qr' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        // Shipping
        Setting::set('shipping_inside_dhaka_fee', $request->inside);
        Setting::set('shipping_outside_dhaka_fee', $request->outside);

        // Branding text fields
        $brandingFields = [
            'site_name', 'footer_description',
            'facebook_url', 'instagram_url', 'youtube_url',
            'support_phone', 'support_email',
            'bkash_number', 'nagad_number',
        ];

        foreach ($brandingFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field) ?? '');
            }
        }

        // Logo upload
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('branding', 'public');
            Setting::set('site_logo', $path);
        }

        // Favicon upload
        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('branding', 'public');
            Setting::set('site_favicon', $path);
        }

        // QR Code Uploads
        if ($request->hasFile('bkash_qr')) {
            $path = $request->file('bkash_qr')->store('branding/payment', 'public');
            Setting::set('bkash_qr', $path);
        }

        if ($request->hasFile('nagad_qr')) {
            $path = $request->file('nagad_qr')->store('branding/payment', 'public');
            Setting::set('nagad_qr', $path);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
