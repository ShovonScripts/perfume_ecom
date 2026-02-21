<x-app-layout>
    <div class="bg-white dark:bg-gray-950 min-h-screen pb-20" x-data="{ mobileFiltersOpen: false }">
        
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Shop All Fragrances</h1>
                <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">Discover your signature scent from our premium collection.</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Sidebar Filters (Desktop) -->
                <aside class="hidden lg:block w-64 flex-shrink-0 space-y-8">
                    @include('shop.partials.filters')
                </aside>

                <!-- Mobile Filter Dialog -->
                <div x-show="mobileFiltersOpen" 
                     class="fixed inset-0 z-40 lg:hidden" 
                     role="dialog" 
                     aria-modal="true">
                    
                    <div x-show="mobileFiltersOpen" 
                         x-transition:enter="transition-opacity ease-linear duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black bg-opacity-25" 
                         @click="mobileFiltersOpen = false"></div>

                    <div x-show="mobileFiltersOpen" 
                         x-transition:enter="transition ease-in-out duration-300 transform"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-300 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white dark:bg-gray-900 py-4 pb-12 shadow-xl">
                        
                        <div class="flex items-center justify-between px-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Filters</h2>
                            <button type="button" 
                                    class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md bg-white dark:bg-gray-900 p-2 text-gray-400"
                                    @click="mobileFiltersOpen = false">
                                <span class="sr-only">Close menu</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="mt-4 px-4">
                            @include('shop.partials.filters', ['mobile' => true])
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1">
                    <!-- Top Bar: Sort & Toggle -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-6 mb-8">
                        <button type="button" 
                                class="-m-2 ml-4 p-2 text-gray-400 hover:text-gray-500 sm:ml-6 lg:hidden"
                                @click="mobileFiltersOpen = true">
                            <span class="sr-only">Filters</span>
                            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="hidden lg:block">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $products->total() }}</span> results
                            </p>
                        </div>

                        <div class="flex items-center">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <div>
                                    <button type="button" 
                                            class="group inline-flex justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                                            @click="open = !open" 
                                            @click.away="open = false">
                                        Sort
                                        <svg class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                     role="menu" style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort', 'newest') === 'newest' ? 'font-bold text-gray-900 dark:text-white' : '' }}">Newest</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') === 'oldest' ? 'font-bold text-gray-900 dark:text-white' : '' }}">Oldest</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') === 'price_asc' ? 'font-bold text-gray-900 dark:text-white' : '' }}">Price: Low to High</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request('sort') === 'price_desc' ? 'font-bold text-gray-900 dark:text-white' : '' }}">Price: High to Low</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                            @foreach($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-24">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Try adjusting your filters or search criteria.</p>
                            <a href="{{ route('shop.index') }}" class="mt-6 inline-block px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
