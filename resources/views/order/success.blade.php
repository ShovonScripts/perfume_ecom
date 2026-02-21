<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Placed Successfully') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Success Icon -->
                    <div class="text-center mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h1 class="text-3xl font-bold text-green-600 dark:text-green-400 mt-4">Order Placed Successfully!</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Thank you for your order</p>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Order Details</h2>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Number:</span>
                                <span class="font-semibold">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Status:</span>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="font-semibold">Cash on Delivery</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Date:</span>
                                <span class="font-semibold">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Shipping Information</h2>
                        
                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">Name:</span>
                                <p class="font-semibold">{{ $order->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">Phone:</span>
                                <p class="font-semibold">{{ $order->phone }}</p>
                            </div>
                            @if($order->email)
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">Email:</span>
                                <p class="font-semibold">{{ $order->email }}</p>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">Shipping Address:</span>
                                <p class="font-semibold">{{ $order->address_line }}, {{ $order->area }}, {{ $order->city }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">Shipping Zone:</span>
                                <p class="font-semibold">{{ $order->shipping_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-4">Price Summary</h2>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping Fee:</span>
                                <span>৳{{ number_format($order->shipping_fee, 2) }}</span>
                            </div>
                            @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount:</span>
                                <span>-৳{{ number_format($order->discount, 2) }}</span>
                            </div>
                            @endif
                            <hr class="border-gray-300 dark:border-gray-600">
                            <div class="flex justify-between text-xl font-bold">
                                <span>Grand Total:</span>
                                <span class="text-blue-600 dark:text-blue-400">৳{{ number_format($order->grand_total, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Note:</strong> The delivery charge must be paid in advance to confirm the order.
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('home') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 shadow-lg">
                        Back to Home
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
