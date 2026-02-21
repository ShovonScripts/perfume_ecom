<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">My Wishlist</h1>

            @if($items->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($items as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="relative aspect-square bg-gray-100 dark:bg-gray-900/50">
                            @if($item->product->thumbnail_image)
                                <img src="{{ asset('storage/'.$item->product->thumbnail_image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            <!-- Remove Button -->
                            <form action="{{ route('wishlist.toggle', $item->product) }}" method="POST" class="absolute top-3 right-3">
                                @csrf
                                <button type="submit" class="bg-white/90 dark:bg-gray-800/90 text-red-500 hover:text-red-600 p-2 rounded-full shadow-sm hover:shadow-md transition-all backdrop-blur-sm transform hover:scale-110" title="Remove from Wishlist">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                </button>
                            </form>
                        </div>

                        <div class="p-4">
                            @if($item->product->brand)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">{{ $item->product->brand->name }}</div>
                            @endif
                            <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-1">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $item->product->name }}
                                </a>
                            </h3>
                            
                            <div class="flex items-center justify-between mt-4">
                                <span class="font-bold text-gray-900 dark:text-white">
                                    ৳{{ number_format($item->product->price) }}
                                </span>
                                <a href="{{ route('product.show', $item->product->slug) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View Details ->
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Your wishlist is empty</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Save items you want to see again here. Just click the heart icon on any product.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
