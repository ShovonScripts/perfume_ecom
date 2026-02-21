@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('account.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order History</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($orders as $order)
                <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    
                    {{-- Order Info --}}
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-900 dark:text-white">#{{ $order->order_number }}</span>
                            <span class="text-xs text-gray-400 font-medium px-2 py-0.5 border border-gray-200 dark:border-gray-600 rounded-full">
                                {{ $order->items_count ?? $order->orderItems->count() }} items
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    {{-- Status Badge --}}
                    <div>
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

                    {{-- Total + Action --}}
                    <div class="text-right flex items-center gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                            <p class="font-bold text-gray-900 dark:text-white">৳{{ number_format($order->grand_total, 2) }}</p>
                        </div>
                        {{-- We'll add view details link in step 34.3 --}}
                        <a href="{{ route('account.orders.show', $order) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                            View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No orders found</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">You haven't placed any orders yet.</p>
                </div>
                @endforelse
            </div>
            
            {{-- Pagination --}}
            @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
