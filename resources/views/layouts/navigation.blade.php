<nav x-data="{ open: false, searchOpen: false }" class="sticky top-0 z-50 w-full backdrop-blur-xl bg-white/80 dark:bg-gray-900/80 border-b border-gray-100/50 dark:border-gray-700/50 transition-all duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo & Brand -->
            <div class="shrink-0 flex items-center gap-8">
                @php
                    $navLogo = \App\Models\Setting::get('site_logo');
                    $navSiteName = \App\Models\Setting::get('site_name', 'Perfume Store');
                @endphp
                <a href="{{ route('home') }}" class="group flex items-center gap-2">
                    @if($navLogo)
                        <img src="{{ asset('storage/'.$navLogo) }}" alt="{{ $navSiteName }}" class="block h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                    @else
                        <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300">{{ $navSiteName }}</span>
                    @endif
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-all">
                        Home
                    </a>
                    <a href="{{ route('shop.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-all">
                        Shop
                    </a>
                    <a href="{{ route('categories.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-black dark:hover:text-white rounded-lg hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition-all">
                        Categories
                    </a>
                </div>
            </div>

            <!-- Search Bar (Centered & Stylish) -->
            <div class="hidden md:flex flex-1 max-w-lg mx-8"
                 x-data="liveSearch()"
                 @click.away="showResults = false">
                <div class="relative w-full group">
                    <div class="relative z-10">
                        <input type="text"
                               x-model="query"
                               @input.debounce.400ms="search()"
                               @focus="if(results.length) showResults = true"
                               placeholder="Search for perfumes..."
                               class="w-full pl-11 pr-4 py-2.5 text-sm bg-gray-100/50 dark:bg-gray-800/50 border-0 ring-1 ring-gray-200 dark:ring-gray-700 rounded-full text-gray-900 dark:text-white placeholder-gray-500 focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <!-- Loading spinner -->
                        <svg x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div x-show="showResults && (results.length > 0 || (query.length >= 3 && !loading))"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute top-12 left-0 right-0 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-xl z-[60] overflow-hidden backdrop-blur-3xl ring-1 ring-black/5">
                        
                        <div class="max-h-[70vh] overflow-y-auto custom-scrollbar">
                           <template x-if="results.length > 0">
                                <div>
                                    <template x-for="item in results" :key="item.id">
                                        <a :href="'/product/' + item.slug"
                                           class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700/50 last:border-0 group transition-colors">
                                            <!-- Thumbnail -->
                                            <div class="relative w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                                <template x-if="item.thumbnail">
                                                    <img :src="item.thumbnail" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                                </template>
                                                <template x-if="!item.thumbnail">
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                       <i data-lucide="image" class="w-5 h-5"></i>
                                                    </div>
                                                </template>
                                            </div>
                                            <!-- Info -->
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="item.name"></h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="item.brand"></p>
                                            </div>
                                            <!-- Price -->
                                            <template x-if="item.price">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md">
                                                    ৳<span x-text="Number(item.price).toLocaleString()"></span>
                                                </span>
                                            </template>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <template x-if="results.length === 0 && query.length >= 3 && !loading">
                                <div class="px-4 py-8 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-3 text-gray-400">
                                        <i data-lucide="search-x" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">No products found</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try searching for something else</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                
                {{-- Search Toggle (Mobile) --}}
                <button @click="searchOpen = !searchOpen" class="md:hidden p-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i data-lucide="search" class="w-6 h-6"></i>
                </button>

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors group">
                    <i data-lucide="shopping-bag" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                    @php
                        $cartCount = count(session('cart.items', []));
                    @endphp
                    @if($cartCount > 0)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-indigo-600 rounded-full border-2 border-white dark:border-gray-800 shadow-sm animate-pulse-once">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <!-- Wishlist -->
                <!-- Wishlist -->
                <a href="{{ route('account.wishlist') }}" class="relative p-2 text-gray-500 hover:text-pink-500 dark:text-gray-400 dark:hover:text-pink-400 transition-colors group">
                    <i data-lucide="heart" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                    @auth
                        @if(auth()->user()->wishlist()->count() > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-pink-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        @endif
                    @endauth
                </a>

                @auth

                    <!-- User Dropdown -->
                    <div class="relative ml-2" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="flex items-center gap-2 focus:outline-none group">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 p-0.5 shadow-md group-hover:shadow-lg transition-all">
                                <div class="w-full h-full rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-sm font-bold text-gray-800 dark:text-white uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                        </button>

                        <div x-show="dropdownOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50 overflow-hidden">
                            
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/50">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                @if(Auth::user()->is_admin)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                                        {{ Auth::user()->role_label }}
                                    </span>
                                @endif
                            </div>

                            <div class="py-1">
                                <a href="{{ route('account.orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <i data-lucide="package" class="w-4 h-4"></i>
                                    My Orders
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <i data-lucide="settings" class="w-4 h-4"></i>
                                    Settings
                                </a>
                            </div>

                            <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Guest Auth Buttons -->
                    <div class="hidden sm:flex items-center gap-3 ml-2">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white transition-colors">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-black dark:bg-white dark:text-black rounded-full hover:bg-gray-800 dark:hover:bg-gray-100 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            Register
                        </a>
                    </div>
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="sm:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        </div>


    <!-- Mobile Search Overlay -->
    <div x-show="searchOpen"
         @click.away="searchOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute top-20 left-0 w-full bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-lg p-4 z-40 md:hidden"
         x-data="liveSearch()">
        <div class="relative">
             <input type="text"
                   x-model="query"
                   @input.debounce.400ms="search()"
                   placeholder="Search products..."
                   class="w-full pl-10 pr-10 py-3 text-base bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
             <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i data-lucide="search" class="w-5 h-5"></i>
             </div>
             <button @click="searchOpen = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
             </button>
        </div>

         <div x-show="results.length > 0" class="mt-4 bg-white dark:bg-gray-900 rounded-xl shadow-inner border border-gray-100 dark:border-gray-700 overflow-hidden max-h-[60vh] overflow-y-auto">
            <template x-for="item in results" :key="item.id">
                <a :href="'/product/' + item.slug" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 border-b dark:border-gray-800 last:border-0">
                    <div class="w-10 h-10 rounded-md overflow-hidden bg-gray-100 shrink-0">
                        <img :src="item.thumbnail" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                        <p class="text-xs text-gray-500" x-text="item.brand"></p>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 shadow-xl max-h-[80vh] overflow-y-auto">
        
        <!-- Mobile Search -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700" x-data="liveSearch()">
            <div class="relative">
                <input type="text"
                       x-model="query"
                       @input.debounce.400ms="search()"
                       placeholder="Search products..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>
            
            <!-- Mobile Search Results -->
             <div x-show="results.length > 0" class="mt-2 bg-white dark:bg-gray-900 rounded-xl shadow-inner border border-gray-100 dark:border-gray-700 overflow-hidden">
                <template x-for="item in results" :key="item.id">
                    <a :href="'/product/' + item.slug" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 border-b dark:border-gray-800 last:border-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                        <p class="text-xs text-gray-500" x-text="item.brand"></p>
                    </a>
                </template>
            </div>
        </div>

        <div class="py-2 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Home
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">
                Shop
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                Categories
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
            @auth
                <div class="px-4 flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1 px-2">

                    
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-red-600 dark:text-red-400">
                             <div class="flex items-center gap-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Log Out
                            </div>
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 space-y-3">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 bg-black dark:bg-white text-white dark:text-black rounded-xl font-bold hover:bg-gray-900 dark:hover:bg-gray-100 transition shadow-lg">
                        Create Account
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

<!-- Live Search Alpine Component -->
<script>
function liveSearch() {
    return {
        query: '',
        results: [],
        loading: false,
        showResults: false,
        search() {
            if (this.query.length < 3) {
                this.results = [];
                this.showResults = false;
                return;
            }
            this.loading = true;
            fetch(`/search?q=${encodeURIComponent(this.query)}`)
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.showResults = true;
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
