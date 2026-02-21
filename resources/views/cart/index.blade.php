<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

                    @if(count($items) > 0)
                        @foreach ($items as $item)
                            <div class="border dark:border-gray-600 rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-700">
                            <div class="flex justify-between items-start gap-4">
                                <!-- Image -->
                                <div class="w-20 h-20 shrink-0 bg-gray-100 dark:bg-gray-600 rounded-md overflow-hidden">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ asset('storage/'.$item['image']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold dark:text-gray-100">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Size: {{ $item['ml_value'] }}{{ $item['ml_unit'] }}
                                    </p>
                                    <p class="text-sm mt-2">
                                        Unit Price: <span class="font-semibold">৳{{ number_format($item['unit_price'], 2) }}</span>
                                    </p>
                                    <p class="text-sm">
                                        Quantity: <span class="font-semibold">{{ $item['quantity'] }}</span>
                                    </p>
                                    <p class="text-lg font-bold mt-2 text-blue-600 dark:text-blue-400">
                                        Line Total: ৳{{ number_format($item['line_total'], 2) }}
                                    </p>
                                </div>

                                <div class="ml-4">
                                    <form method="POST" action="{{ route('cart.remove') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="variant_id" value="{{ $item['product_variant_id'] }}">
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition"
                                                onclick="return confirm('Remove this item from cart?');">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Update Quantity Form -->
                            <form method="POST" action="{{ route('cart.update') }}" class="mt-4 flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $item['product_variant_id'] }}">
                                <label class="text-sm">Update Quantity:</label>
                                <input type="number" 
                                       name="quantity" 
                                       value="{{ $item['quantity'] }}" 
                                       min="1"
                                       class="w-20 px-3 py-1 border dark:border-gray-600 rounded dark:bg-gray-600 dark:text-white">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm transition">
                                    Update
                                </button>
                            </form>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-4 text-lg">Your cart is empty.</p>
                            <a href="{{ route('admin.products.index') }}" 
                               class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition">
                                Continue Shopping
                            </a>
                        </div>
                    @endif

                    @if(count($items) > 0)
                        <hr class="my-6 border-gray-300 dark:border-gray-600">

                        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                            <!-- Coupon Section -->
                             <div class="w-full md:w-1/2">
                                <form action="{{ route('cart.applyCoupon') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code" placeholder="Coupon Code" class="border dark:border-gray-600 rounded px-3 py-2 w-full dark:bg-gray-700 dark:text-gray-100" required>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Apply</button>
                                </form>
                                @error('coupon')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                @if($coupon)
                                    <div class="mt-2 text-green-600 flex items-center justify-between bg-green-50 dark:bg-green-900/20 p-2 rounded border border-green-200 dark:border-green-800">
                                        <span>Applied: <strong>{{ $coupon->code }}</strong> (-৳{{ number_format($discount, 2) }})</span>
                                        <form action="{{ route('cart.removeCoupon') }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 ml-2">&times;</button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Totals Section -->
                            <div class="w-full md:w-1/2 text-right">
                                <p class="text-lg">Subtotal: <span class="dark:text-gray-100">৳{{ number_format($subtotal, 2) }}</span></p>
                                @if($discount > 0)
                                    <p class="text-lg text-green-600">Discount: -৳{{ number_format($discount, 2) }}</p>
                                @endif
                                
                                @if(isset($comboDiscount) && $comboDiscount > 0)
                                    <p class="text-lg text-green-600">Combo Discount: -৳{{ number_format($comboDiscount, 2) }}</p>
                                @endif
                                
                                <h2 class="text-2xl font-bold mt-2">
                                    Total: <span class="text-blue-600 dark:text-blue-400">৳{{ number_format($total, 2) }}</span>
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Total items: {{ count($items) }}
                                </p>
                            
                                <div class="mt-6 flex justify-end gap-3">
                                    <a href="{{ route('admin.products.index') }}" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
                                        Continue Shopping
                                    </a>
                                    <a href="{{ route('checkout.show') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-semibold">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
