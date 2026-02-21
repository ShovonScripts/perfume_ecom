@props(['product'])

@php
    $firstVariant = $product->variants->first();
    $variantsJson = $product->variants->map(function($v) {
        return [
            'id' => $v->id,
            'price' => (float)$v->price,
            'compare_price' => $v->compare_price ? (float)$v->compare_price : null,
            'stock' => (int)$v->stock,
            'label' => $v->ml_value . $v->ml_unit . ($v->type ? " ({$v->type})" : "")
        ];
    });
@endphp

<div class="group relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
     x-data="{ 
        selectedVariantId: {{ $firstVariant?->id ?? 'null' }},
        selectedPrice: {{ $firstVariant?->price ?? 0 }},
        comparePrice: {{ $firstVariant?->compare_price ?? 'null' }},
        inStock: {{ ($firstVariant?->stock ?? 0) > 0 ? 'true' : 'false' }},
        variants: @js($variantsJson),
        updateVariant(id) {
            const v = this.variants.find(x => x.id == id);
            if (v) {
                this.selectedVariantId = v.id;
                this.selectedPrice = v.price;
                this.comparePrice = v.compare_price;
                this.inStock = v.stock > 0;
            }
        }
     }">
    
    <!-- Image Container -->
    <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
        <a href="{{ route('product.show', $product->slug) }}">
            @if($product->thumbnail_image)
                <img src="{{ asset('storage/'.$product->thumbnail_image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            @endif
        </a>

        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-2">
            @if($product->created_at->diffInDays(now()) < 30)
                <span class="px-2.5 py-1 bg-black/80 dark:bg-white/90 backdrop-blur-md text-white dark:text-black text-[10px] font-bold uppercase tracking-wider rounded-md">
                    New
                </span>
            @endif
            <template x-if="comparePrice && comparePrice > selectedPrice">
                <span class="px-2.5 py-1 bg-red-500/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider rounded-md">
                    -<span x-text="Math.round(((comparePrice - selectedPrice) / comparePrice) * 100)"></span>%
                </span>
            </template>
        </div>

        <!-- Wishlist Button (Top Right) -->
        <button class="absolute top-3 right-3 p-2 rounded-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm text-gray-400 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 transition-all transform translate-x-12 group-hover:translate-x-0 duration-300 shadow-sm"
                @auth onclick="toggleWishlist({{ $product->id }})" @else onclick="window.location.href='{{ route('login') }}'" @endauth>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </button>

        <!-- Quick Action (Bottom Center) -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center opacity-100 md:opacity-0 md:group-hover:opacity-100 transform translate-y-0 md:translate-y-4 md:group-hover:translate-y-0 transition-all duration-300">
            <template x-if="selectedVariantId && inStock">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariantId">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="bg-black dark:bg-white text-white dark:text-black px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wide hover:scale-105 transition-transform shadow-lg">
                        Add to Cart
                    </button>
                </form>
            </template>
            <template x-if="!selectedVariantId || !inStock">
                <span class="bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wide cursor-not-allowed">
                    Out of Stock
                </span>
            </template>
        </div>
    </div>

    <!-- Product Details -->
    <div class="p-4">
        <div class="mb-1 flex justify-between items-start">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $product->brand->name ?? 'Brand' }}</span>
            @if($product->variants->count() > 1)
                <select x-on:change="updateVariant($event.target.value)" 
                        class="text-[10px] py-0 px-2 h-5 border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 rounded focus:ring-0 focus:border-indigo-500 appearance-none bg-none pr-4 relative">
                    <template x-for="v in variants" :key="v.id">
                        <option :value="v.id" x-text="v.label" :selected="v.id == selectedVariantId"></option>
                    </template>
                </select>
            @elseif($product->variants->count() == 1)
                <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">{{ $firstVariant->ml_value }}{{ $firstVariant->ml_unit }}</span>
            @endif
        </div>
        <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
            <a href="{{ route('product.show', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>
        
        <div class="flex items-baseline gap-2 mt-2">
            <span class="text-base font-bold text-gray-900 dark:text-white">
                ৳<span x-text="Number(selectedPrice).toLocaleString()"></span>
            </span>
            <template x-if="comparePrice && comparePrice > selectedPrice">
                <span class="text-xs text-gray-400 line-through">
                    ৳<span x-text="Number(comparePrice).toLocaleString()"></span>
                </span>
            </template>
        </div>
    </div>
</div>
