@extends('admin.layouts.app')
@section('title', 'Customer: ' . $user->name)

@section('content')
<div class="space-y-6">

    <!-- Customer Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        @if($user->is_blocked)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">🚫 Blocked</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">✅ Active</span>
                        @endif
                        <span class="text-xs text-gray-400 dark:text-gray-500">Joined {{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <form method="POST" action="{{ route('admin.customers.block', $user) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('{{ $user->is_blocked ? 'Unblock' : 'Block' }} this customer?')"
                            class="{{ $user->is_blocked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-500 hover:bg-red-600' }} text-white px-5 py-2 rounded-xl text-sm font-medium transition">
                        {{ $user->is_blocked ? '✅ Unblock Customer' : '🚫 Block Customer' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Orders</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $user->orders_count }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Spent</p>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳{{ number_format($user->orders_sum_grand_total ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5 text-center">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Order Value</p>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                ৳{{ $user->orders_count > 0 ? number_format(($user->orders_sum_grand_total ?? 0) / $user->orders_count) : 0 }}
            </p>
        </div>
    </div>

    <!-- Admin Note -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Admin Note</h3>
        <form method="POST" action="{{ route('admin.customers.updateNote', $user) }}" class="flex gap-3">
            @csrf
            <textarea name="admin_note" rows="2" placeholder="Add internal note about this customer..."
                      class="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 p-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $user->admin_note }}</textarea>
            <button type="submit" class="self-end bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-medium transition">
                Save
            </button>
        </form>
    </div>

    <!-- Order History -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order History</h3>
        </div>

        @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Order #</th>
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
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">{{ $order->order_number }}</a>
                        </td>
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
                        <td class="py-3 px-4 text-center text-gray-500 dark:text-gray-400 text-xs">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.orders.show', $order) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $orders->links() }}</div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">No orders yet from this customer</p>
        </div>
        @endif
    </div>

</div>
@endsection
