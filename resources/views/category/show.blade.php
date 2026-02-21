<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                <a href="/" class="hover:text-blue-600 transition">Home</a>
                <span class="mx-2">›</span>
                @if($category->parent)
                    <a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-blue-600 transition">{{ $category->parent->name }}</a>
                    <span class="mx-2">›</span>
                @endif
                <span class="text-gray-800 dark:text-gray-200">{{ $category->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- ========== FILTER SIDEBAR ========== -->
                <aside class="lg:col-span-1">
                    <form method="GET" action="{{ route('category.show', $category->slug) }}" id="filter-form">

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 p-5 sticky top-24">

                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filters
                            </h3>

                            <!-- Subcategories -->
                            @if($subcategories->count() > 0)
                            <div class="mb-5">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Subcategories</h4>
                                <div class="space-y-1">
                                    @foreach($subcategories as $sub)
                                    <a href="{{ route('category.show', $sub->slug) }}"
                                       class="block px-3 py-2 text-sm rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition text-gray-700 dark:text-gray-300">
                                        {{ $sub->name }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700 mb-5">
                            @endif

                            <!-- Brand Filter -->
                            @if($brands->count() > 0)
                            <div class="mb-5">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Brand</h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($brands as $brand)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox"
                                               name="brands[]"
                                               value="{{ $brand->id }}"
                                               {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition">
                                            {{ $brand->name }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700 mb-5">
                            @endif

                            <!-- Price Range -->
                            <div class="mb-5">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Price Range</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <input type="number"
                                               name="min_price"
                                               placeholder="Min ৳"
                                               value="{{ request('min_price') }}"
                                               class="w-full text-sm px-3 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="number"
                                               name="max_price"
                                               placeholder="Max ৳"
                                               value="{{ request('max_price') }}"
                                               class="w-full text-sm px-3 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Sort (hidden, synced from top bar) -->
                            <input type="hidden" name="sort" id="sort-input" value="{{ request('sort', 'latest') }}">

                            <!-- Apply Button -->
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                Apply Filters
                            </button>

                            <!-- Clear Filters -->
                            @if(request()->hasAny(['brands', 'min_price', 'max_price', 'sort']))
                            <a href="{{ route('category.show', $category->slug) }}"
                               class="block text-center text-sm text-gray-500 hover:text-red-500 mt-3 transition">
                                Clear All Filters
                            </a>
                            @endif
                        </div>
                    </form>
                </aside>

                <!-- ========== PRODUCT GRID ========== -->
                <div class="lg:col-span-3">

                    <!-- Top Bar: Count + Sort -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $products->total() }} {{ Str::plural('product', $products->total()) }} found
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Sort by:</label>
                            <select onchange="document.getElementById('sort-input').value = this.value; document.getElementById('filter-form').submit();"
                                    class="text-sm px-3 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Category Description -->
                    @if($category->description)
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $category->description }}</p>
                    </div>
                    @endif

                    <!-- Product Grid -->
                    @if($products->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-6">
                        @foreach($products as $product)
                        <a href="{{ route('product.show', $product->slug) }}"
                           class="group bg-white dark:bg-gray-800 rounded-2xl border dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">

                            <!-- Product Image -->
                            <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700 relative">
                                @if($product->thumbnail_image)
                                    <img src="{{ asset('storage/'.$product->thumbnail_image) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Discount Badge -->
                                @if($product->variants->first()?->compare_price && $product->variants->first()->compare_price > $product->variants->first()->price)
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    -{{ round((($product->variants->first()->compare_price - $product->variants->first()->price) / $product->variants->first()->compare_price) * 100) }}%
                                </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <!-- Brand -->
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ $product->brand->name }}</span>

                                <!-- Name -->
                                <h3 class="text-sm lg:text-base font-semibold text-gray-900 dark:text-white mt-1 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                    {{ $product->name }}
                                </h3>

                                <!-- Variants count -->
                                @if($product->variants->count() > 1)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $product->variants->count() }} sizes available
                                </p>
                                @endif

                                <!-- Price -->
                                <div class="mt-2 flex items-baseline gap-2">
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                        ৳{{ number_format($product->variants->min('price')) }}
                                    </span>
                                    @if($product->variants->first()?->compare_price && $product->variants->first()->compare_price > $product->variants->first()->price)
                                    <span class="text-xs text-gray-400 line-through">
                                        ৳{{ number_format($product->variants->first()->compare_price) }}
                                    </span>
                                    @endif
                                </div>

                                <!-- Stock indicator -->
                                @php $totalStock = $product->variants->sum('stock'); @endphp
                                @if($totalStock <= 0)
                                    <span class="inline-block mt-2 text-xs text-red-600 font-medium">Out of Stock</span>
                                @elseif($totalStock < 5)
                                    <span class="inline-block mt-2 text-xs text-orange-600 font-medium">Low Stock</span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                    @else
                    <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border dark:border-gray-700">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">No products found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Try adjusting your filters or browse other categories</p>
                        @if(request()->hasAny(['brands', 'min_price', 'max_price']))
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="inline-block mt-4 text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Clear all filters →
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
