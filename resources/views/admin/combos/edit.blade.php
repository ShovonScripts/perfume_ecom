@extends('admin.layouts.app')
@section('title', 'Edit Combo')

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@php
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'variants' => $product->variants->where('is_active', true)->map(function($v) {
                return [
                    'id' => $v->id,
                    'name' => trim($v->ml_value . ' ' . $v->ml_unit . ' ' . $v->type),
                    'price' => (float) $v->price
                ];
            })->values()
        ];
    })->values();
@endphp
<script>
    function comboManager() {
        return {
            variants: [
                @foreach($combo->items as $item)
                {
                    id: {{ $item->variant->id }},
                    product_name: "{{ addslashes($item->variant->product->name) }}",
                    variant_name: "{{ trim($item->variant->ml_value . ' ' . $item->variant->ml_unit . ' ' . $item->variant->type) }}",
                    price: {{ $item->variant->price }}
                },
                @endforeach
            ],
            availableProducts: @json($formattedProducts),
            selectedProduct: '',
            selectedVariant: '',

            get originalTotal() {
                return this.variants.reduce((total, v) => total + v.price, 0);
            },
            
            comboPrice: '{{ $combo->combo_price }}',
            
            get discountPercentage() {
                let total = this.originalTotal;
                let cp = parseFloat(this.comboPrice);
                if (total > 0 && cp > 0 && cp < total) {
                    return Math.round(((total - cp) / total) * 100);
                }
                return 0;
            },

            addVariant() {
                if (!this.selectedProduct || !this.selectedVariant) return;
                
                let product = this.availableProducts.find(p => p.id == this.selectedProduct);
                if (!product) return;
                
                let variant = product.variants.find(v => v.id == this.selectedVariant);
                if (!variant) return;

                // Check if already added
                if(this.variants.find(v => v.id == variant.id)) return;

                this.variants.push({
                    id: variant.id,
                    product_name: product.name,
                    variant_name: variant.name,
                    price: variant.price
                });

                this.selectedProduct = '';
                this.selectedVariant = '';
            },

            removeVariant(index) {
                this.variants.splice(index, 1);
            },
            
            slugify(text) {
                return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
            },
            
            name: '{{ addslashes($combo->name) }}',
            slug: '{{ $combo->slug }}',
            
            updateSlug() {
                // this.slug = this.slugify(this.name);
            }
        }
    }
</script>
@endpush

@section('content')
<div class="max-w-4xl" x-data="comboManager()">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Combo</h2>

        @if($errors->any())
            <div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.combos.update', $combo) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-5">
                    <!-- Name & Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Combo Name *</label>
                        <input type="text" name="name" x-model="name" @input="updateSlug" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug *</label>
                        <input type="text" name="slug" x-model="slug" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $combo->description }}</textarea>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Combo Image</label>
                        @if($combo->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/'.$combo->image) }}" class="w-24 h-24 object-cover rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="w-full border rounded-xl py-2 px-3 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    </div>

                    <!-- Pricing -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Combo Price *</label>
                        <input type="number" step="0.01" name="combo_price" x-model="comboPrice" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl border dark:border-gray-600">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Original Total:</span>
                            <span class="font-bold dark:text-white" x-text="'৳' + originalTotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <span class="font-bold text-green-600" x-show="discountPercentage > 0" x-text="discountPercentage + '% OFF'"></span>
                            <span class="font-bold text-gray-400" x-show="discountPercentage <= 0">0%</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $combo->sort_order }}" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex items-center mt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $combo->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Items) -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Select Products</h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl border dark:border-gray-600 mb-4 space-y-3">
                        <div>
                            <select x-model="selectedProduct" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm">
                                <option value="">-- Choose Product --</option>
                                <template x-for="product in availableProducts" :key="product.id">
                                    <option :value="product.id" x-text="product.name"></option>
                                </template>
                            </select>
                        </div>
                        <div x-show="selectedProduct">
                            <select x-model="selectedVariant" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm">
                                <option value="">-- Choose Variant --</option>
                                <template x-for="variant in (availableProducts.find(p => p.id == selectedProduct)?.variants || [])" :key="variant.id">
                                    <option :value="variant.id" x-text="variant.name + ' - ৳' + variant.price"></option>
                                </template>
                            </select>
                        </div>
                        <button type="button" @click="addVariant" class="w-full bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2 rounded-lg text-sm transition" :disabled="!selectedProduct || !selectedVariant">
                            Add to Combo
                        </button>
                    </div>

                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Combo Items:</h4>
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        <template x-for="(v, index) in variants" :key="v.id">
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg shadow-sm">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="v.product_name"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="v.variant_name"></p>
                                    <input type="hidden" name="variants[]" :value="v.id">
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300" x-text="'৳'+v.price"></span>
                                    <button type="button" @click="removeVariant(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <p x-show="variants.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">No products added yet.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-3 border-t dark:border-gray-700 pt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition">
                    Update Combo
                </button>
                <a href="{{ route('admin.combos.index') }}" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 text-gray-700 dark:text-gray-200 px-6 py-2.5 rounded-xl font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
