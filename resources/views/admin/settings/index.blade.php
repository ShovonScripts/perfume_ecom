@extends('admin.layouts.app')
@section('title', 'Settings')

@section('content')
<div class="max-w-4xl">

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SHIPPING CONFIGURATION                         --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl mb-8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center">
                        <i data-lucide="truck" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Shipping Fees</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Configure delivery charges</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="inside" class="block text-sm font-medium mb-2">Inside Dhaka (৳)</label>
                        <input type="number" name="inside" id="inside"
                               value="{{ old('inside', $inside) }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               required>
                        @error('inside')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="outside" class="block text-sm font-medium mb-2">Outside Dhaka (৳)</label>
                        <input type="number" name="outside" id="outside"
                               value="{{ old('outside', $outside) }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               required>
                        @error('outside')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        <strong>Note:</strong> Applied automatically at checkout based on customer's city.
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- BRANDING & IDENTITY                            --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl mb-8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/40 rounded-xl flex items-center justify-center">
                        <i data-lucide="palette" class="w-5 h-5 text-violet-600 dark:text-violet-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Branding & Identity</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Logo, favicon, site name & description</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Site Name --}}
                    <div>
                        <label for="site_name" class="block text-sm font-medium mb-2">Site Name</label>
                        <input type="text" name="site_name" id="site_name"
                               value="{{ old('site_name', $site_name) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="My Store">
                        @error('site_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Footer Description --}}
                    <div>
                        <label for="footer_description" class="block text-sm font-medium mb-2">Footer Description</label>
                        <textarea name="footer_description" id="footer_description" rows="2"
                                  class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Short tagline for the footer">{{ old('footer_description', $footer_description) }}</textarea>
                        @error('footer_description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Site Logo</label>
                        @if($site_logo)
                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg inline-block">
                                <img src="{{ asset('storage/'.$site_logo) }}" alt="Current Logo" class="h-12 object-contain">
                            </div>
                        @endif
                        <input type="file" name="site_logo" accept="image/*"
                               class="w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300
                                      hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 transition">
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, SVG, WebP — Max 2MB</p>
                        @error('site_logo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Favicon Upload --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Favicon</label>
                        @if($site_favicon)
                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg inline-block">
                                <img src="{{ asset('storage/'.$site_favicon) }}" alt="Current Favicon" class="h-8 w-8 object-contain">
                            </div>
                        @endif
                        <input type="file" name="site_favicon" accept="image/png,image/x-icon,image/svg+xml"
                               class="w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300
                                      hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 transition">
                        <p class="text-xs text-gray-400 mt-1">PNG, ICO, SVG — Max 512KB</p>
                        @error('site_favicon')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- PAYMENT SETTINGS (MANUAL)                      --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl mb-8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Payment Methods</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manual numbers and QR codes for bKash / Nagad</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium mb-2">bKash Number</label>
                        <input type="text" name="bkash_number"
                               value="{{ old('bkash_number', $bkash_number) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">bKash QR Code (1:1 image)</label>
                        <input type="file" name="bkash_qr" class="w-full text-sm text-gray-500 dark:text-gray-400 border dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none">
                        
                        @if($bkash_qr)
                            <img src="{{ asset('storage/'.$bkash_qr) }}" class="mt-3 w-40 h-40 object-cover rounded border dark:border-gray-600">
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Nagad Number</label>
                        <input type="text" name="nagad_number"
                               value="{{ old('nagad_number', $nagad_number) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Nagad QR Code (1:1 image)</label>
                        <input type="file" name="nagad_qr" class="w-full text-sm text-gray-500 dark:text-gray-400 border dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none">

                        @if($nagad_qr)
                            <img src="{{ asset('storage/'.$nagad_qr) }}" class="mt-3 w-40 h-40 object-cover rounded border dark:border-gray-600">
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SOCIAL & CONTACT                               --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl mb-8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/40 rounded-xl flex items-center justify-center">
                        <i data-lucide="share-2" class="w-5 h-5 text-pink-600 dark:text-pink-400"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Social & Contact</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Links shown in the footer</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium mb-2">Facebook URL</label>
                        <input type="url" name="facebook_url" id="facebook_url"
                               value="{{ old('facebook_url', $facebook_url) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="https://facebook.com/yourpage">
                        @error('facebook_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="instagram_url" class="block text-sm font-medium mb-2">Instagram URL</label>
                        <input type="url" name="instagram_url" id="instagram_url"
                               value="{{ old('instagram_url', $instagram_url) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="https://instagram.com/yourpage">
                        @error('instagram_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="youtube_url" class="block text-sm font-medium mb-2">YouTube URL</label>
                        <input type="url" name="youtube_url" id="youtube_url"
                               value="{{ old('youtube_url', $youtube_url) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="https://youtube.com/@yourchannel">
                        @error('youtube_url')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="support_phone" class="block text-sm font-medium mb-2">Support Phone</label>
                        <input type="text" name="support_phone" id="support_phone"
                               value="{{ old('support_phone', $support_phone) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="+880 1XXXXXXXXX">
                        @error('support_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="support_email" class="block text-sm font-medium mb-2">Support Email</label>
                        <input type="email" name="support_email" id="support_email"
                               value="{{ old('support_email', $support_email) }}"
                               class="w-full px-4 py-2.5 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="support@yourstore.com">
                        @error('support_email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-semibold transition shadow-lg hover:shadow-xl flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
