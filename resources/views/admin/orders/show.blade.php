@extends('admin.layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-4xl space-y-6">

    <!-- Order Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Order {{ $order->order_number }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                        'packed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
                        'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300',
                        'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($order->status) }}
                </span>
                <a href="{{ route('admin.orders.invoice', $order) }}"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Invoice PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Customer Information
        </h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Name:</span> <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $order->name }}</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Phone:</span> <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $order->phone }}</span></div>
            @if($order->email)
            <div><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $order->email }}</span></div>
            @endif
            <div><span class="text-gray-500 dark:text-gray-400">Zone:</span> <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $order->shipping_zone === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</span></div>
            <div class="col-span-2"><span class="text-gray-500 dark:text-gray-400">Address:</span> <span class="font-medium text-gray-900 dark:text-white ml-2">{{ $order->address_line }}, {{ $order->area }}, {{ $order->city }}</span></div>
        </div>
    </div>

    <!-- Payment Verification -->
    @if($order->payment_status === 'pending_verification' || $order->payment_status === 'paid')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Payment Verification
        </h3>
        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div><span class="text-gray-500 dark:text-gray-400">Sender Number:</span> <span class="font-mono font-bold text-gray-900 dark:text-white ml-2">{{ $order->payment_sender_number }}</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Amount:</span> <span class="font-bold text-gray-900 dark:text-white ml-2">৳{{ number_format($order->payment_amount, 2) }}</span></div>
            <div class="col-span-2"><span class="text-gray-500 dark:text-gray-400">Transaction ID:</span> <span class="font-mono font-bold text-gray-900 dark:text-white ml-2">{{ $order->payment_transaction_id ?? 'N/A' }}</span></div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                <span class="ml-2 px-2 py-1 rounded-full text-xs font-bold {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                </span>
            </div>
        </div>

        @if($order->payment_status === 'pending_verification')
        <form method="POST" action="{{ route('admin.orders.verify', $order) }}">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
                Verify Payment & Process Order
            </button>
        </form>
        @endif
    </div>
    @endif

    <!-- Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Order Items</h3>
        </div>
        <div class="divide-y dark:divide-gray-700">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between p-4">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $item->name_snapshot }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Size: {{ $item->ml_value }}{{ $item->ml_unit }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">৳{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                    <p class="font-bold text-gray-900 dark:text-white">৳{{ number_format($item->line_total, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Price Summary -->
        <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>Shipping Fee</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                <span>Discount</span><span>-৳{{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white border-t dark:border-gray-600 pt-2">
                <span>Grand Total</span><span class="text-indigo-600 dark:text-indigo-400">৳{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        @if($order->delivery_charge_prepaid)
        <div class="px-6 pb-4">
            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                <p class="text-sm text-yellow-800 dark:text-yellow-200"><strong>Note:</strong> Delivery charge prepaid for outside Dhaka.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Change Status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Change Order Status</h3>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-3 items-end">
            @csrf
            @method('PUT')
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Status</label>
                <select name="status" id="status"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    @foreach(['pending','processing','packed','shipped','delivered','cancelled'] as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition">
                Update Status
            </button>
        </form>
    </div>

</div>
@endsection
