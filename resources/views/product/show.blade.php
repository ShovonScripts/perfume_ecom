<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-8 md:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
             x-data="{
                selectedVariant: {{ $product->variants->first()?->id ?? 'null' }},
                selectedPrice: {{ $product->variants->first()?->price ?? 0 }},
                comparePrice: {{ $product->variants->first()?->compare_price ?? 0 }},
                selectedStock: {{ $product->variants->first()?->stock ?? 0 }},
                selectedSku: '{{ $product->variants->first()?->sku ?? '' }}',
                selectedLabel: '{{ $product->variants->first() ? $product->variants->first()->ml_value . $product->variants->first()->ml_unit : '' }}',
                mainImage: '{{ $product->thumbnail_image ? asset("storage/".$product->thumbnail_image) : "" }}',
                quantity: 1,
                addedToCart: false,
             }">

            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                <a href="/" class="hover:text-blue-600 transition">Home</a>
                <span class="mx-2">›</span>
                <a href="#" class="hover:text-blue-600 transition">{{ $product->category->name }}</a>
                <span class="mx-2">›</span>
                <span class="text-gray-800 dark:text-gray-200">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">

                <!-- ========== IMAGE SECTION ========== -->
                <div>
                    <!-- Main Image -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg border dark:border-gray-700 aspect-square flex items-center justify-center">
                        <template x-if="mainImage">
                            <img :src="mainImage"
                                 alt="{{ $product->name }} - Buy Online in Bangladesh"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-all duration-300">
                        </template>
                        <template x-if="!mainImage">
                            <div class="text-gray-400 text-center">
                                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm">No image available</p>
                            </div>
                        </template>
                    </div>

                    <!-- Gallery Thumbnails -->
                    @if($product->images->count() > 0 || $product->thumbnail_image)
                    <div class="flex gap-3 mt-4 overflow-x-auto pb-2">
                        @if($product->thumbnail_image)
                        <button @click="mainImage = '{{ asset('storage/'.$product->thumbnail_image) }}'"
                                class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 dark:border-gray-600 hover:border-blue-500 transition focus:border-blue-500 focus:outline-none">
                            <img src="{{ asset('storage/'.$product->thumbnail_image) }}"
                                 alt="{{ $product->name }} - Buy Online in Bangladesh"
                                 loading="lazy"
                                 class="w-full h-full object-cover">
                        </button>
                        @endif

                        @foreach($product->images as $image)
                        <button @click="mainImage = '{{ asset('storage/'.$image->image_path) }}'"
                                class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 dark:border-gray-600 hover:border-blue-500 transition focus:border-blue-500 focus:outline-none">
                            <img src="{{ asset('storage/'.$image->image_path) }}"
                                 alt="{{ $product->name }} - Buy Online in Bangladesh"
                                 loading="lazy"
                                 class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- ========== PRODUCT INFO SECTION ========== -->
                <div>
                    <!-- Brand Badge -->
                    <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                        {{ $product->brand->name }}
                    </span>

                    <!-- Product Name -->
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ $product->name }}
                    </h1>

                    <!-- Category -->
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Category: {{ $product->category->name }}
                    </p>

                    <!-- Short Description -->
                    @if($product->short_description)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                        {{ $product->short_description }}
                    </p>
                    @endif

                    <!-- Price Display -->
                    <div class="bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-xl p-4 mb-6">
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                ৳<span x-text="Number(selectedPrice).toLocaleString()"></span>
                            </span>
                            <template x-if="comparePrice > 0 && comparePrice > selectedPrice">
                                <span class="text-lg text-gray-400 line-through">
                                    ৳<span x-text="Number(comparePrice).toLocaleString()"></span>
                                </span>
                            </template>
                            <template x-if="comparePrice > 0 && comparePrice > selectedPrice">
                                <span class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 text-xs font-bold px-2 py-1 rounded-full">
                                    -<span x-text="Math.round(((comparePrice - selectedPrice) / comparePrice) * 100)"></span>%
                                </span>
                            </template>
                        </div>
                        <!-- SKU -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            SKU: <span x-text="selectedSku"></span>
                        </p>
                    </div>

                    <!-- Variant Selector -->
                    @if($product->variants->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">Select Size</h3>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($product->variants as $variant)
                            <label class="relative cursor-pointer"
                                   :class="selectedVariant === {{ $variant->id }}
                                        ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30 border-blue-500'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-blue-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                   class="block border rounded-xl p-4 transition-all duration-200">
                                <input type="radio"
                                       name="variant_radio"
                                       value="{{ $variant->id }}"
                                       class="sr-only"
                                       x-on:change="
                                           selectedVariant = {{ $variant->id }};
                                           selectedPrice = {{ $variant->price }};
                                           comparePrice = {{ $variant->compare_price ?? 0 }};
                                           selectedStock = {{ $variant->stock }};
                                           selectedSku = '{{ $variant->sku }}';
                                           selectedLabel = '{{ $variant->ml_value }}{{ $variant->ml_unit }}';
                                           quantity = 1;
                                       "
                                       {{ $loop->first ? 'checked' : '' }}>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                             :class="selectedVariant === {{ $variant->id }} ? 'border-blue-500' : 'border-gray-300 dark:border-gray-500'">
                                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500" x-show="selectedVariant === {{ $variant->id }}"></div>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $variant->ml_value }}{{ $variant->ml_unit }}</span>
                                            @if($variant->type)
                                            <span class="text-xs text-gray-500 ml-1">({{ $variant->type }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-gray-900 dark:text-white">৳{{ number_format($variant->price) }}</span>
                                        @if($variant->compare_price && $variant->compare_price > $variant->price)
                                        <span class="text-xs text-gray-400 line-through ml-1">৳{{ number_format($variant->compare_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <!-- Stock Info -->
                                <div class="mt-2 ml-8">
                                    @if($variant->stock <= 0)
                                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Out of Stock</span>
                                    @elseif($variant->stock < 5)
                                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">Only {{ $variant->stock }} left!</span>
                                    @else
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">In Stock</span>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Stock Warning Bar -->
                    <template x-if="selectedStock > 0 && selectedStock < 5">
                        <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 rounded-lg p-3 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-orange-800 dark:text-orange-200 font-medium">
                                Hurry! Only <span x-text="selectedStock"></span> items left in stock
                            </span>
                        </div>
                    </template>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="{{ route('cart.add') }}" class="mt-4" @submit="addedToCart = true; setTimeout(() => addedToCart = false, 2000)">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedVariant">

                        <div class="flex items-center gap-4">
                            <!-- Quantity Selector -->
                            <div class="flex items-center border dark:border-gray-600 rounded-xl overflow-hidden">
                                <button type="button"
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition font-bold text-lg">
                                    −
                                </button>
                                <input type="number"
                                       name="quantity"
                                       x-model="quantity"
                                       min="1"
                                       :max="selectedStock"
                                       class="w-16 text-center border-0 focus:ring-0 dark:bg-gray-800 dark:text-white text-lg font-semibold"
                                       readonly>
                                <button type="button"
                                        @click="quantity = Math.min(selectedStock, quantity + 1)"
                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition font-bold text-lg">
                                    +
                                </button>
                            </div>

                            <!-- Add to Cart Button -->
                            <button type="submit"
                                    :disabled="selectedStock <= 0"
                                    :class="selectedStock <= 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl'"
                                    class="flex-1 text-white font-bold py-3.5 px-8 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-lg">
                                <template x-if="!addedToCart">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                        </svg>
                                        <span x-text="selectedStock <= 0 ? 'Out of Stock' : 'Add to Cart'"></span>
                                    </span>
                                </template>
                                <template x-if="addedToCart">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Added!
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>

                    <!-- Wishlist Toggle -->
                    @auth
                    <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="mt-4">
                        @csrf
                        @php
                            $inWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists();
                        @endphp
                        <button type="submit" class="flex items-center gap-2 text-sm font-medium transition-colors {{ $inWishlist ? 'text-red-500 hover:text-red-600' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                            <svg class="w-6 h-6 {{ $inWishlist ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span>{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}</span>
                        </button>
                    </form>
                    @endauth

                    <!-- Cart Link (after adding) -->
                    @if(session('success'))
                    <div class="mt-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-3 flex items-center justify-between">
                        <span class="text-green-700 dark:text-green-300 text-sm font-medium">{{ session('success') }}</span>
                        <a href="{{ route('cart.index') }}" class="text-green-700 dark:text-green-300 underline text-sm font-bold hover:text-green-900">View Cart →</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Long Description Section -->
            @if($product->long_description)
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 p-6 lg:p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Product Details</h2>
                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                    {!! nl2br(e($product->long_description)) !!}
                </div>
            </div>
            @endif

            <!-- Review Section -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 p-6 lg:p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Customer Reviews</h3>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Review List -->
                    <div>
                        <div class="flex items-center gap-4 mb-8">
                            <div class="text-5xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($product->reviews->avg('rating'), 1) }}
                            </div>
                            <div>
                                <div class="flex text-yellow-400 mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= round($product->reviews->avg('rating')))
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->reviews->count() }} Reviews</div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            @if($product->reviews->count() > 0)
                                @foreach ($product->reviews as $review)
                                <div class="border-b border-gray-100 dark:border-gray-700 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $review->user->name }}</div>
                                </div>
                                <div class="flex text-yellow-400 mb-2 text-xs">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                    <span class="text-gray-400 ml-2">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ $review->review_text }}</p>
                                </div>
                                @endforeach
                            @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">No reviews yet. Be the first to share your thoughts!</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Review Form -->
                    <div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-8 rounded-xl border border-gray-100 dark:border-gray-700 sticky top-24">
                            @auth
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Write a Review</h4>
                                <form method="POST" action="{{ route('reviews.store', $product) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                        <div class="flex gap-4">
                                            @for($i=5; $i>=1; $i--)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" required>
                                                <div class="text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors">
                                                    <div class="flex flex-col items-center">
                                                        <span class="text-2xl">★</span>
                                                        <span class="text-xs">{{ $i }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Review</label>
                                        <textarea name="review_text" rows="5" placeholder="What did you like or dislike?" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                    </div>

                                    <button type="submit" class="w-full bg-black hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 text-white font-bold py-3 px-6 rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        Submit Review
                                    </button>
                                </form>
                            @else
                                <div class="text-center">
                                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">Login to Review</h4>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Share your experience with other customers.</p>
                                    <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                                        Login Now
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
