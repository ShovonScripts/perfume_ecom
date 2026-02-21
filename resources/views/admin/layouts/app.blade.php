<!DOCTYPE html>
<html lang="en" class="h-full" x-data="adminTheme()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 h-full">

{{-- ━━━ Alpine Data ━━━ --}}
<script>
function adminTheme() {
    return {
        darkMode: localStorage.getItem('admin_dark') === 'true',
        sidebarOpen: localStorage.getItem('admin_sidebar') !== 'false',
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('admin_dark', this.darkMode);
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('admin_sidebar', this.sidebarOpen);
        }
    }
}
</script>

<div class="flex h-screen overflow-hidden">

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- SIDEBAR                                            --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-[70px]'"
        class="flex-shrink-0 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 text-white flex flex-col transition-all duration-300 ease-in-out overflow-hidden relative">

        {{-- Subtle top glow --}}
        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-indigo-600/5 to-transparent pointer-events-none"></div>

        {{-- ─── Logo / Brand ─── --}}
        <div class="flex items-center gap-3 px-4 h-16 border-b border-white/[.06] relative z-10 flex-shrink-0">
            <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/20">
                <i data-lucide="zap" class="w-5 h-5 text-white"></i>
            </div>
            <span x-show="sidebarOpen" x-cloak
                  x-transition:enter="transition-opacity duration-200 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-[17px] font-bold text-white tracking-tight whitespace-nowrap">
                Ecom<span class="text-indigo-400">Admin</span>
            </span>
        </div>

        {{-- ─── Navigation ─── --}}
        <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden sidebar-scrollbar relative z-10 space-y-0.5">

            {{-- ── Dashboard ── --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            {{-- ── SECTION: Orders ── --}}
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">Orders</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            {{-- Orders (simple link) --}}
            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="clipboard-list" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">All Orders</span>
            </a>

            {{-- ── SECTION: Catalog (Manager+) ── --}}
            @if(auth()->user()->hasRole('super_admin', 'manager'))
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">Catalog</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            {{-- ▸ Products (collapsible submenu) --}}
            @php $catalogOpen = request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*'); @endphp
            <div x-data="{ open: {{ $catalogOpen ? 'true' : 'false' }} }" class="mx-2">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150
                               {{ $catalogOpen ? 'bg-white/[.08] text-white' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                    <i data-lucide="package" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="whitespace-nowrap flex-1 text-left">Products</span>
                    <svg x-show="sidebarOpen" class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Submenu --}}
                <div x-show="open && sidebarOpen" x-cloak
                     x-transition:enter="transition-all ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition-all ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="ml-[26px] mt-1 space-y-0.5 border-l border-gray-700/50 pl-3">

                    <a href="{{ route('admin.products.index') }}"
                       class="block py-2 px-2.5 rounded-md text-[13px] transition-colors duration-150
                              {{ request()->routeIs('admin.products.index') ? 'text-indigo-400 bg-indigo-500/10 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">
                        All Products
                    </a>
                    <a href="{{ route('admin.products.create') }}"
                       class="block py-2 px-2.5 rounded-md text-[13px] transition-colors duration-150
                              {{ request()->routeIs('admin.products.create') ? 'text-indigo-400 bg-indigo-500/10 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">
                        Add Product
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="block py-2 px-2.5 rounded-md text-[13px] transition-colors duration-150
                              {{ request()->routeIs('admin.categories.*') ? 'text-indigo-400 bg-indigo-500/10 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">
                        Categories
                    </a>
                    <a href="{{ route('admin.brands.index') }}"
                       class="block py-2 px-2.5 rounded-md text-[13px] transition-colors duration-150
                              {{ request()->routeIs('admin.brands.*') ? 'text-indigo-400 bg-indigo-500/10 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">
                        Brands
                    </a>
                    <a href="{{ route('admin.hero-banners.index') }}"
                       class="block py-2 px-2.5 rounded-md text-[13px] transition-colors duration-150
                              {{ request()->routeIs('admin.hero-banners.*') ? 'text-indigo-400 bg-indigo-500/10 font-semibold' : 'text-gray-500 hover:text-gray-300' }}">
                        Hero Banners
                    </a>
                </div>
            </div>

            {{-- ── SECTION: CRM ── --}}
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">CRM</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            <a href="{{ route('admin.customers.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="users" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Customers</span>
            </a>

            {{-- ── SECTION: Marketing ── --}}
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">Marketing</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            <a href="{{ route('admin.combos.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.combos.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="layers" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Combos</span>
            </a>

            <a href="{{ route('admin.coupons.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.coupons.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="ticket" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Coupons</span>
            </a>

            <a href="{{ route('admin.reviews.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.reviews.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="star" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Reviews</span>
            </a>
            @endif

            {{-- ── SECTION: Operations (Super Admin) ── --}}
            @if(auth()->user()->isSuperAdmin())
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">Operations</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            <a href="{{ route('admin.inventory.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.inventory.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="warehouse" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Inventory</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="settings" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Settings</span>
            </a>

            {{-- ── SECTION: Admin ── --}}
            <div x-show="sidebarOpen" x-cloak class="px-4 pt-5 pb-1">
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-gray-600">Admin</p>
            </div>
            <div x-show="!sidebarOpen" class="pt-3 mx-2">
                <div class="h-px bg-gray-700/50"></div>
            </div>

            <a href="{{ route('admin.staff.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 mx-2 rounded-lg text-[13px] font-medium transition-all duration-150
                      {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/[.06] hover:text-white' }}">
                <i data-lucide="shield-check" class="w-[18px] h-[18px] flex-shrink-0"></i>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Staff & Roles</span>
            </a>
            @endif

        </nav>

        {{-- ─── User Info Footer ─── --}}
        <div class="border-t border-white/[.06] p-3 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/20">
                    <span class="text-white text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div x-show="sidebarOpen" x-cloak class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    @php
                        $roleColors = ['super_admin'=>'text-red-400','manager'=>'text-blue-400','staff'=>'text-gray-400'];
                    @endphp
                    <p class="text-xs {{ $roleColors[auth()->user()->role] ?? 'text-gray-400' }} font-medium">
                        {{ auth()->user()->role_label }}
                    </p>
                </div>
            </div>
        </div>

    </aside>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT AREA                                  --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- ─── TOP NAVBAR ─── --}}
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700/80 flex-shrink-0 z-20">
            <div class="flex items-center justify-between px-6 h-16">

                {{-- Left: Toggle + Page Title --}}
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar()"
                            class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                        <i data-lucide="panel-left" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800 dark:text-white leading-tight">
                            @yield('title', 'Dashboard')
                        </h1>
                    </div>
                </div>

                {{-- Right: Actions Row --}}
                <div class="flex items-center gap-2">

                    {{-- Dark Mode Toggle --}}
                    <button @click="toggleDark()"
                            class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors"
                            title="Toggle Dark Mode">
                        <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                        <i x-show="darkMode" x-cloak data-lucide="sun" class="w-5 h-5"></i>
                    </button>

                    {{-- Visit Store --}}
                    <a href="{{ route('home') }}" target="_blank"
                       class="hidden sm:flex p-2 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors"
                       title="Visit Store">
                        <i data-lucide="external-link" class="w-5 h-5"></i>
                    </a>

                    {{-- Divider --}}
                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block mx-1"></div>

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/60 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block">{{ auth()->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden sm:block"></i>
                        </button>

                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition flex items-center gap-2.5">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        {{-- ─── PAGE CONTENT ─── --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm"
                     x-data="{ show: true }" x-show="show"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @yield('content')

        </main>

    </div>
</div>

@stack('scripts')
<script>lucide.createIcons();</script>
</body>
</html>
