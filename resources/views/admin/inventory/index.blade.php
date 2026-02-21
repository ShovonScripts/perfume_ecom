@extends('admin.layouts.app')
@section('title', 'Inventory Management')

@section('content')
<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Variants</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalVariants }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Units</p>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalUnits) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center cursor-pointer hover:ring-2 hover:ring-yellow-400 transition" onclick="window.location='?filter=low'">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Low Stock</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $lowStock }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center cursor-pointer hover:ring-2 hover:ring-red-400 transition" onclick="window.location='?filter=out'">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Out of Stock</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $outOfStock }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search Product</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Product name..."
                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Stock Filter</label>
                <select name="filter" class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="low" {{ request('filter') == 'low' ? 'selected' : '' }}>Low Stock (&lt;5)</option>
                    <option value="out" {{ request('filter') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="healthy" {{ request('filter') == 'healthy' ? 'selected' : '' }}>Healthy (≥5)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sort By</label>
                <select name="sort" class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stock: Low → High</option>
                    <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock: High → Low</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Product Name</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            <a href="{{ route('admin.inventory.index') }}" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-5 py-2 rounded-xl text-sm font-medium transition">Reset</a>
            <a href="{{ route('admin.inventory.export') }}"
               class="ml-auto inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        @if($variants->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Product</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Variant</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Price</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Current Stock</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Update Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($variants as $variant)
                    <tr class="border-b dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition {{ $variant->stock === 0 ? 'bg-red-50/50 dark:bg-red-900/10' : ($variant->stock < 5 ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '') }}">
                        <td class="py-3 px-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $variant->product->name ?? 'N/A' }}</p>
                            @if($variant->product?->brand)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $variant->product->brand->name }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-gray-700 dark:text-gray-300">{{ $variant->ml_value }}{{ $variant->ml_unit }}</td>
                        <td class="py-3 px-4 text-center font-semibold text-gray-900 dark:text-white">৳{{ number_format($variant->price) }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-2xl font-bold {{ $variant->stock === 0 ? 'text-red-600' : ($variant->stock < 5 ? 'text-yellow-600' : 'text-emerald-600') }}">
                                {{ $variant->stock }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($variant->stock === 0)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Out of Stock</span>
                            @elseif($variant->stock < 5)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">Low Stock</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">In Stock</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <form method="POST" action="{{ route('admin.inventory.update') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                <input type="number" name="new_stock" value="{{ $variant->stock }}" min="0"
                                       class="w-20 text-sm text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white py-1.5 focus:ring-2 focus:ring-indigo-500">
                                <input type="text" name="reason" placeholder="Reason"
                                       class="w-28 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white py-1.5 px-2 focus:ring-2 focus:ring-indigo-500">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition flex-shrink-0">Save</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $variants->links() }}</div>
        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-lg text-gray-500 dark:text-gray-400">No variants found</p>
        </div>
        @endif
    </div>

    <!-- Recent Stock Changes -->
    @if($recentLogs->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Recent Stock Changes
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Product</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Change</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Reason</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">By</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                    <tr class="border-b dark:border-gray-700">
                        <td class="py-3 px-4 text-gray-900 dark:text-white">
                            {{ $log->variant->product->name ?? 'N/A' }}
                            <span class="text-xs text-gray-500">({{ $log->variant->ml_value ?? '' }}{{ $log->variant->ml_unit ?? '' }})</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="font-bold {{ $log->change_amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log->change_amount > 0 ? '+' : '' }}{{ $log->change_amount }}
                            </span>
                            <span class="text-xs text-gray-400 ml-1">({{ $log->old_stock }} → {{ $log->new_stock }})</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-xs">{{ $log->reason }}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-xs">{{ $log->admin->name ?? 'System' }}</td>
                        <td class="py-3 px-4 text-center text-gray-500 dark:text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
