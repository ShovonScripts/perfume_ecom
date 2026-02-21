@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Order #, phone, or name..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    @foreach(['pending','processing','packed','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition shadow-sm">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-5 py-2 rounded-xl text-sm font-medium transition">Reset</a>
            @can('manager')
            <a href="{{ route('admin.orders.export', request()->query()) }}"
               class="ml-auto inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            @endcan
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        @if($orders->count() > 0)
        <form method="POST" action="{{ route('admin.orders.bulkUpdate') }}" id="bulkForm">
            @csrf
            <!-- Bulk Actions Bar -->
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700 flex items-center gap-3 flex-wrap">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium" id="selectedCount">0 selected</span>
                <select name="bulk_status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1.5 focus:ring-2 focus:ring-indigo-500">
                    @foreach(['pending','processing','packed','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition"
                        onclick="return confirm('Update status of selected orders?')">Update Selected</button>
                <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">{{ $orders->total() }} total orders</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                            <th class="py-3 px-4 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Order #</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</th>
                            <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="border-b dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                       class="order-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $order->name }}</td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $order->phone }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->grand_total) }}</td>
                            <td class="py-3 px-4 text-center">
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
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center text-gray-500 dark:text-gray-400 text-xs">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span class="text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $orders->links() }}</div>
        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg text-gray-500 dark:text-gray-400">No orders found</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const countEl = document.getElementById('selectedCount');
    function updateCount() {
        const checked = document.querySelectorAll('.order-checkbox:checked').length;
        countEl.textContent = checked + ' selected';
    }
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateCount();
        });
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
});
</script>
@endpush
@endsection
