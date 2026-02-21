@extends('layouts.app')
@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('account.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Orders
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
            <div>
                <a href="#" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center gap-2 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Invoice
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Order Items --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Order Items ({{ $order->orderItems->count() }})</h2>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            ];
                            $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($order->orderItems as $item)
                        <div class="p-6 flex gap-4">
                            {{-- Product Image --}}
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200 dark:border-gray-600">
                                @if($item->product && $item->product->thumbnail_image)
                                    <img src="{{ asset('storage/'.$item->product->thumbnail_image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                            @if($item->product)
                                                <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-indigo-600 transition-colors">
                                                    {{ $item->product->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">Product Unavailable</span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $item->ml_value }}{{ $item->ml_unit }} 
                                            @if($item->product_variant_id)
                                                &bull; Variant
                                            @endif
                                        </p>
                                    </div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        ৳{{ number_format($item->line_total, 2) }}
                                    </p>
                                </div>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    ৳{{ number_format($item->unit_price, 2) }} &times; {{ $item->quantity }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>৳{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span>৳{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                            <span>Discount</span>
                            <span>-৳{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="pt-2 flex justify-between font-bold text-lg text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-600 mt-2">
                            <span>Grand Total</span>
                            <span>৳{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="space-y-6">
                
                {{-- Shipping Info --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Shipping Details</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Name</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->billing_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Phone</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->billing_phone }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Email</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->billing_email }}</p>
                        </div>
                         <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Address</p>
                            <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $order->billing_address }}</p>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ ucwords(str_replace('_', ' ', $order->shipping_area)) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Payment Info</h3>
                     <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Payment Method</p>
                            <p class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                @if($order->payment_method == 'cod')
                                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Cash On Delivery
                                @else
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> {{ ucfirst($order->payment_method) }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium mb-1">Payment Status</p>
                            <p class="font-medium">
                                @if($order->payment_status == 'paid')
                                    <span class="text-green-600 dark:text-green-400">Paid</span>
                                @else
                                    <span class="text-yellow-600 dark:text-yellow-400">Unpaid / Pending</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Need help with this order?</p>
                    <a href="{{ url('/contact') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">Contact Support</a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
