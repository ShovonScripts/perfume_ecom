<x-app-layout>
    <div class="bg-white dark:bg-gray-950 min-h-screen">
        
        <!-- Header -->
        <div class="relative bg-black py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1615634260167-c8cdede054de?q=80&w=2574&auto=format&fit=crop" alt="Background" class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
            </div>
            <div class="relative max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">Explore Collections</h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">Browse our carefully curated categories to find the perfect scent for every occasion.</p>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categories as $category)
                <div class="group relative overflow-hidden rounded-2xl shadow-xl bg-gray-100 dark:bg-gray-900 h-64 md:h-72">
                    <!-- Background Image (Placeholder or Actual) -->
                     <div class="absolute inset-0 bg-gray-300 dark:bg-gray-800 transition-transform duration-700 group-hover:scale-110">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @else
                            {{-- Placeholder --}}
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center">
                                <span class="text-9xl opacity-5 select-none font-serif">{{ substr($category->name, 0, 1) }}</span>
                            </div>
                        @endif
                     </div>
                     
                     <!-- Overlay -->
                     <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
                     
                     <!-- Content -->
                     <div class="absolute inset-0 flex flex-col justify-end p-8">
                        <h2 class="text-3xl font-bold text-white mb-2 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            {{ $category->name }}
                        </h2>
                        <p class="text-gray-300 mb-6 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-100">
                            {{ $category->products_count }} Products
                        </p>
                        
                        <!-- Subcategories -->
                        @if($category->children->count() > 0)
                        <div class="space-y-2 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-200">
                             @foreach($category->children as $child)
                                <a href="{{ route('category.show', $child->slug) }}" class="block text-sm text-gray-300 hover:text-white hover:underline decoration-indigo-500 underline-offset-4">
                                    {{ $child->name }}
                                </a>
                             @endforeach
                        </div>
                        @else
                           <a href="{{ route('category.show', $category->slug) }}" class="inline-flex items-center text-white font-bold opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-200">
                                Explore Collection <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                           </a>
                        @endif
                     </div>
                     
                     <!-- Link Overlay -->
                     <a href="{{ route('category.show', $category->slug) }}" class="absolute inset-0 z-10"></a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
