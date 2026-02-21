@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name or email..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sort By</label>
                <select name="sort" class="text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="top_spender" {{ request('sort') == 'top_spender' ? 'selected' : '' }}>Top Spender</option>
                    <option value="most_orders" {{ request('sort') == 'most_orders' ? 'selected' : '' }}>Most Orders</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-5 py-2 rounded-xl text-sm font-medium transition">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        @if($customers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Orders</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Spent</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Joined</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr class="border-b dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 transition">{{ $customer->name }}</span>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $customer->email }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $customer->orders_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500' }}">
                                {{ $customer->orders_count }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">
                            ৳{{ number_format($customer->orders_sum_grand_total ?? 0) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($customer->is_blocked)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Blocked</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Active</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center text-gray-500 dark:text-gray-400 text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">View</a>
                                <form method="POST" action="{{ route('admin.customers.block', $customer) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('{{ $customer->is_blocked ? 'Unblock' : 'Block' }} this customer?')"
                                            class="{{ $customer->is_blocked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-500 hover:bg-red-600' }} text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                        {{ $customer->is_blocked ? 'Unblock' : 'Block' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $customers->links() }}</div>
        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-lg text-gray-500 dark:text-gray-400">No customers found</p>
        </div>
        @endif
    </div>
</div>
@endsection
