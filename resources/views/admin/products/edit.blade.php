@extends('admin.layouts.app')
@section('title', 'Edit Product')

@push('scripts')
<script>
    function previewThumbnail(input) {
        const preview = document.getElementById('thumbnail-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function previewGallery(input) {
        const container = document.getElementById('gallery-preview');
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-20 h-20 object-cover rounded-lg border dark:border-gray-600';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
<script>
    function variantManager(initialVariants) {
        return {
            variants: (initialVariants && initialVariants.length > 0) ? initialVariants : [{
                id: null,
                ml_value: '',
                ml_unit: 'ml',
                type: '',
                price: '',
                compare_price: '',
                stock: '',
                sku: '',
                sort_order: 0,
                is_active: true
            }],
            add() {
                this.variants.push({
                    id: null,
                    ml_value: '',
                    ml_unit: 'ml',
                    type: '',
                    price: '',
                    compare_price: '',
                    stock: '',
                    sku: '',
                    sort_order: this.variants.length,
                    is_active: true
                });
            },
            remove(index) {
                this.variants = this.variants.filter((_, i) => i !== index);
            }
        }
    }
</script>
@endpush

@section('content')
<div class="max-w-3xl">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Product: {{ $product->name }}</h2>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           required>
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Brand & Category -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Brand <span class="text-red-500">*</span>
                        </label>
                        <select name="brand_id" id="brand_id"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->parent ? $category->parent->name . ' → ' : '' }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                    <textarea name="short_description" id="short_description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('short_description', $product->short_description) }}</textarea>
                    @error('short_description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Long Description -->
                <div>
                    <label for="long_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Long Description</label>
                    <textarea name="long_description" id="long_description" rows="6"
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('long_description', $product->long_description) }}</textarea>
                    @error('long_description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Images -->
                <div class="border-t dark:border-gray-700 pt-5">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Product Images</h3>

                    <!-- Current Thumbnail -->
                    @if($product->thumbnail_image)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Thumbnail</label>
                        <div class="inline-block p-3 bg-gray-50 dark:bg-gray-700 rounded-xl relative">
                            <img src="{{ asset('storage/'.$product->thumbnail_image) }}" alt="Thumbnail" class="max-h-32 rounded-lg">
                            <label class="flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_thumbnail" value="1" class="w-4 h-4">
                                Remove thumbnail
                            </label>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $product->thumbnail_image ? 'Replace Thumbnail' : 'Thumbnail Image' }}
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center hover:border-indigo-500 transition">
                                <input type="file" name="thumbnail_image" id="thumbnail_image" accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300"
                                       onchange="previewThumbnail(this)">
                                <img id="thumbnail-preview" class="mt-3 mx-auto max-h-24 rounded-lg hidden">
                            </div>
                            @error('thumbnail_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add Gallery Images</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center hover:border-purple-500 transition">
                                <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900/50 dark:file:text-purple-300"
                                       onchange="previewGallery(this)">
                                <div id="gallery-preview" class="flex flex-wrap gap-2 mt-3 justify-center"></div>
                            </div>
                            @error('gallery_images.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Current Gallery -->
                    @if($product->images->count() > 0)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Gallery (hover to delete)</label>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                            @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/'.$image->image_path) }}" alt="Gallery" class="w-full h-20 object-cover rounded-lg border dark:border-gray-600">
                                <label class="absolute inset-0 bg-red-600/70 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="mr-1">
                                    <span class="text-white text-xs font-bold">Delete</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Variants (Alpine.js) -->
                <div class="border-t dark:border-gray-700 pt-5" x-data='variantManager(@json($product->variants))'>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Product Variants</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Add at least one variant (Size/Price/Stock)</p>
                        </div>
                        <button type="button" @click="add()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Variant
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="border dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/50 relative">
                                <button type="button" @click="remove(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                
                                <!-- Hidden ID for existing variants -->
                                <!-- Hidden ID for existing variants -->
                                <template x-if="variant.id">
                                    <input type="hidden" :name="`variants[${index}][id]`" :value="variant.id">
                                </template>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <!-- ML Value -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Size (Value)</label>
                                        <input type="number" step="0.01" x-model="variant.ml_value" :name="`variants[${index}][ml_value]`" placeholder="50" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white" required>
                                    </div>
                                    <!-- Unit -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Unit</label>
                                        <input type="text" x-model="variant.ml_unit" :name="`variants[${index}][ml_unit]`" placeholder="ml" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white">
                                    </div>
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type (EDP/EDT)</label>
                                        <input type="text" x-model="variant.type" :name="`variants[${index}][type]`" placeholder="EDP" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white">
                                    </div>
                                    <!-- Price -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price *</label>
                                        <input type="number" step="0.01" x-model="variant.price" :name="`variants[${index}][price]`" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white" required>
                                    </div>
                                    <!-- Compare Price -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Old Price</label>
                                        <input type="number" step="0.01" x-model="variant.compare_price" :name="`variants[${index}][compare_price]`" placeholder="0.00" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white">
                                    </div>
                                    <!-- Stock -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Stock *</label>
                                        <input type="number" x-model="variant.stock" :name="`variants[${index}][stock]`" placeholder="0" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white" required>
                                    </div>
                                    <!-- SKU -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">SKU *</label>
                                        <input type="text" x-model="variant.sku" :name="`variants[${index}][sku]`" placeholder="SKU-123" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 dark:text-white" required>
                                    </div>
                                    <!-- Active -->
                                    <div class="flex items-center mt-6">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="variant.is_active" :name="`variants[${index}][is_active]`" class="w-4 h-4 text-indigo-600 rounded">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Active</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="variants.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                            No variants yet. Add one to make the product valid.
                        </div>
                    </div>
                </div>

                <!-- Active -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Product Active</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition">
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-6 py-2.5 rounded-xl font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
