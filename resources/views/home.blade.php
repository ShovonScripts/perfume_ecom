<x-app-layout>

    <!-- ========== HERO SECTION ========== -->
    @if(isset($heroBanners) && $heroBanners->count() > 0)
    <section class="relative bg-black border-b border-gray-800 overflow-hidden group"
             x-data="{ 
                current: 0,
                slides: {{ $heroBanners->count() }},
                timer: null,
                startAutoSlide() {
                    this.timer = setInterval(() => {
                        this.current = (this.current + 1) % this.slides;
                    }, 5000);
                },
                stopAutoSlide() {
                    clearInterval(this.timer);
                    this.timer = null;
                }
             }"
             x-init="startAutoSlide()"
             @mouseenter="stopAutoSlide()"
             @mouseleave="startAutoSlide()">
        
        <div class="relative h-[400px] md:h-[600px] w-full">
            @foreach($heroBanners as $index => $banner)
            <div x-show="current === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-700"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 w-full h-full">
                
                {{-- Background Image --}}
                <div class="absolute inset-0 bg-black">
                    <img src="{{ asset('storage/'.$banner->image_path) }}" 
                         alt="{{ $banner->title }}" 
                         class="w-full h-full object-cover opacity-60">
                </div>
                
                {{-- Standard Gradient --}}
                <div class="absolute inset-0 bg-black/30"></div>

                {{-- Content --}}
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4 sm:px-6 container mx-auto">
                    <div class="max-w-4xl space-y-6"
                         x-show="current === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700 delay-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        
                        @if($banner->title)
                        <h1 class="text-3xl md:text-5xl font-bold text-white tracking-tight drop-shadow-lg leading-tight">
                            {!! nl2br(e($banner->title)) !!}
                        </h1>
                        @endif

                        @if($banner->subtitle)
                        <p class="text-lg md:text-xl text-gray-200 font-medium max-w-2xl mx-auto drop-shadow-md">
                            {{ $banner->subtitle }}
                        </p>
                        @endif

                        @if($banner->button_text && $banner->button_url)
                        <div class="pt-4">
                            <a href="{{ $banner->button_url }}" 
                               class="inline-block bg-white/10 backdrop-blur-md border border-white/50 text-white px-8 py-3 rounded-full font-bold uppercase tracking-widest hover:bg-white hover:text-black transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.6)] transform hover:-translate-y-1">
                                {{ $banner->button_text }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Indicators --}}
        <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-3 z-20">
            @foreach($heroBanners as $index => $banner)
            <button @click="current = {{ $index }}"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="current === {{ $index }} ? 'w-8 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'">
            </button>
            @endforeach
        </div>

        {{-- Controls --}}
        <div class="absolute inset-0 pointer-events-none flex items-center justify-between px-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button @click="current = (current === 0 ? slides - 1 : current - 1)" 
                    class="pointer-events-auto p-2 rounded-full bg-black/30 hover:bg-black/60 text-white transition-all backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="current = (current + 1) % slides" 
                    class="pointer-events-auto p-2 rounded-full bg-black/30 hover:bg-black/60 text-white transition-all backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </section>
    @else
    <!-- Fallback Hero (Static) -->
    <section class="relative h-[400px] md:h-[600px] flex items-center bg-black overflow-hidden">
        <div class="absolute inset-0">
             <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1595181363675-9e65893bd5c9?q=80&w=2670&auto=format&fit=crop')] bg-cover bg-center opacity-60"></div>
             <div class="absolute inset-0 bg-black/30"></div>
        </div>
        <div class="relative container mx-auto px-4 text-center">
            <span class="inline-block py-1.5 px-4 rounded-full border border-white/20 bg-white/10 backdrop-blur-md text-white/80 text-xs font-medium tracking-widest mb-6 uppercase">
                Est. London 2026
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4 tracking-tight drop-shadow-lg">
                Essence of Elegance
            </h1>
            <p class="text-lg text-gray-200 max-w-2xl mx-auto font-medium drop-shadow-md leading-relaxed">
                Discover a curated collection of the world's finest fragrances. 
                Authenticity guaranteed, luxury defined.
            </p>
        </div>
    </section>
    @endif

    <!-- ========== BRAND TICKER ========== -->
    @if(isset($brands) && $brands->count() > 0)
    <div class="bg-black border-b border-gray-800 py-10 overflow-hidden relative">
        <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-black to-transparent z-10"></div>
        <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-black to-transparent z-10"></div>
        
        <div class="flex items-center gap-16 animate-marquee whitespace-nowrap">
            {{-- Double the loop for seamless infinite scroll --}}
            @for ($i = 0; $i < 2; $i++)
                @foreach($brands as $brand)
                <div class="flex-shrink-0 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-500 cursor-pointer">
                    @if($brand->logo)
                        <img src="{{ asset('storage/'.$brand->logo) }}" alt="{{ $brand->name }}" class="h-12 w-auto object-contain">
                    @else
                        <span class="text-2xl font-bold text-white font-serif tracking-widest uppercase">{{ $brand->name }}</span>
                    @endif
                </div>
                @endforeach
            @endfor
        </div>
    </div>
    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 30s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
    @endif

    <!-- ========== CATEGORIES GRID ========== -->
    @if($categories->count() > 0)
    <section class="py-20 md:py-32 bg-white dark:bg-gray-950">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 max-w-3xl mx-auto">
                <span class="text-indigo-600 dark:text-indigo-400 font-bold tracking-widest uppercase text-sm">Collections</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mt-3 mb-6">Find Your Signature Scent</h2>
                <div class="h-1 w-20 bg-indigo-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" class="group relative h-[250px] md:h-[300px] overflow-hidden rounded-3xl shadow-2xl">
                    {{-- Background --}}
                    <div class="absolute inset-0 bg-gray-200 dark:bg-gray-800">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            {{-- Placeholder --}}
                            <div class="w-full h-full bg-gradient-to-br from-indigo-500/20 to-purple-500/20 mix-blend-multiply flex items-center justify-center">
                                <span class="text-8xl opacity-10 font-bold">{{ substr($category->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Hover Reveal Overlay --}}
                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors duration-500"></div>
                    
                    {{-- Content --}}
                    <div class="absolute inset-0 flex flex-col justify-end p-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <h3 class="text-3xl font-bold text-white mb-2 transform group-hover:-translate-y-2 transition-transform duration-500">{{ $category->name }}</h3>
                        <p class="text-white/80 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 delay-100">
                            {{ $category->products_count }} Products
                            <span class="inline-block ml-2 text-indigo-400">→</span>
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ========== COMBO DEALS ========== -->
    @if(isset($combos) && $combos->count() > 0)
    <section class="py-20 bg-gray-50 dark:bg-black border-b dark:border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                     <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">🔥 Combo Deals</h2>
                     <p class="text-gray-500 dark:text-gray-400 mt-2">Bundle & Save with our exclusive combos.</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($combos as $combo)
                <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl overflow-hidden group hover:shadow-xl transition-shadow border dark:border-gray-800 flex flex-col h-full">
                    @php
                        $originalTotal = 0;
                        foreach($combo->items as $item) {
                            $originalTotal += $item->variant->price;
                        }
                        $discount = 0;
                        if ($originalTotal > 0 && $combo->combo_price < $originalTotal) {
                            $discount = round((($originalTotal - $combo->combo_price) / $originalTotal) * 100);
                        }
                    @endphp
                    @if($combo->image)
                        <div class="relative h-64 overflow-hidden bg-gray-100 dark:bg-gray-800">
                            <img src="{{ asset('storage/'.$combo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @if($discount > 0)
                                <div class="absolute top-4 right-4 bg-red-600 text-white font-bold px-3 py-1 rounded-full shadow-lg text-sm">
                                    {{ $discount }}% OFF
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="p-6 flex flex-col flex-grow relative">
                        <!-- Discount badge when no image -->
                        @if(!$combo->image && $discount > 0)
                            <div class="absolute top-4 right-4 bg-red-600 text-white font-bold px-3 py-1 rounded-full shadow-lg text-sm">
                                {{ $discount }}% OFF
                            </div>
                        @endif

                        <div class="flex justify-between items-start mb-2 mt-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $combo->name }}</h3>
                        </div>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex-grow">{{ $combo->description }}</p>

                        <div class="mb-5 space-y-2">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Includes:</h4>
                            <div class="space-y-1">
                                @foreach($combo->items as $item)
                                    <div class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $item->variant->product->name ?? 'Product' }} ({{ $item->variant->ml_value }}{{ $item->variant->ml_unit }})
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-t dark:border-gray-800">
                            <div class="flex justify-between items-end mb-4">
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 line-through">৳{{ number_format($originalTotal, 2) }}</span>
                                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">৳{{ number_format($combo->combo_price, 2) }}</p>
                                </div>
                            </div>
                            
                            <form action="{{ route('combo.add', $combo) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-black dark:bg-white text-white dark:text-black font-semibold py-3 rounded-xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-md flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Add Combo to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ========== NEW ARRIVALS (Using Components) ========== -->
    @if($newArrivals->count() > 0)
    <section class="py-20 bg-gray-50 dark:bg-black">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                     <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">New Arrivals</h2>
                     <p class="text-gray-500 dark:text-gray-400 mt-2">Just dropped. Be the first to own them.</p>
                </div>
                <a href="#" class="hidden md:flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold hover:gap-3 transition-all">
                    View All <span class="text-xl">→</span>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                @foreach($newArrivals as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            
            <div class="mt-8 text-center md:hidden">
                 <a href="#" class="inline-block border border-gray-300 dark:border-gray-700 rounded-full px-8 py-3 text-sm font-bold uppercase tracking-wider hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition-colors">
                    View All Products
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- ========== WHY CHOOSE US ========== -->
    <section class="py-24 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                <div class="space-y-6 p-6 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-300">
                    <div class="w-20 h-20 mx-auto bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">100% Authentic</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        We source directly from authorized distributors. Every bottle is guaranteed original or 2x money back.
                    </p>
                </div>
                <div class="space-y-6 p-6 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-300">
                    <div class="w-20 h-20 mx-auto bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center text-pink-600 dark:text-pink-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fast & Reliable Delivery</h3>
                    <div class="text-gray-500 dark:text-gray-400 leading-relaxed text-sm space-y-3">
                        <div>
                            <strong class="text-gray-800 dark:text-gray-200 block">Same-Day Delivery in Dhaka (Most Orders)</strong>
                            Delivery within 24–48 hours in Dhaka depending on order time and location.
                        </div>
                        <div>
                            <strong class="text-gray-800 dark:text-gray-200 block">Nationwide Delivery</strong>
                            Delivery within 24–72 hours outside Dhaka.
                        </div>
                    </div>
                </div>
                <div class="space-y-6 p-6 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-300">
                    <div class="w-20 h-20 mx-auto bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400">
                         <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Best Price</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Luxury shouldn't break the bank. We offer the most competitive prices in the market.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== NEWSLETTER SECTION ========== -->
    <section class="py-24 bg-black relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1615634260167-c8cdede054de?q=80&w=2574&auto=format&fit=crop')] bg-cover bg-center opacity-20 filter blur-sm"></div>
        <div class="relative container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Join the Inner Circle</h2>
            <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">Subscribe for exclusive early access to drops, member-only pricing, and expert fragrance tips.</p>
            
            <form class="max-w-xl mx-auto flex flex-col sm:flex-row gap-4">
                <input type="email" placeholder="Enter your email address" class="flex-1 rounded-full bg-white/10 border border-white/20 px-6 py-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 backdrop-blur-md">
                <button type="button" class="bg-white text-black font-bold rounded-full px-8 py-4 hover:bg-gray-200 transition-colors shadow-lg">
                    Subscribe
                </button>
            </form>
            <p class="text-gray-500 text-sm mt-6">We respect your inbox. No spam, ever.</p>
        </div>
    </section>

</x-app-layout>
