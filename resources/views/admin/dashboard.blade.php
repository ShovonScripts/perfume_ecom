@extends('admin.layouts.app')
@section('title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function revenueChart() {
    return {
        init() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chartData = @json($last7Days);
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.date),
                    datasets: [
                        {
                            label: 'Revenue (৳)',
                            data: chartData.map(d => d.revenue),
                            backgroundColor: 'rgba(99, 102, 241, 0.3)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Orders',
                            data: chartData.map(d => d.orders),
                            type: 'line',
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgb(168, 85, 247)',
                            pointRadius: 4,
                            tension: 0.3,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => '৳' + v.toLocaleString() }
                        },
                        y1: {
                            position: 'right',
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: { stepSize: 1 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
}
</script>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Today Orders</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $todayOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Today Revenue</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">৳{{ number_format($todayRevenue) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Orders</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $pendingOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">This Month</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-1">৳{{ number_format($monthRevenue) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stats Row -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl p-5 text-white">
            <p class="text-sm text-indigo-200 font-medium">Total Orders</p>
            <p class="text-3xl font-bold mt-1">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-xl p-5 text-white">
            <p class="text-sm text-green-200 font-medium">Total Revenue</p>
            <p class="text-3xl font-bold mt-1">৳{{ number_format($totalRevenue) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl p-5 text-white">
            <p class="text-sm text-purple-200 font-medium">Active Products</p>
            <p class="text-3xl font-bold mt-1">{{ number_format($totalProducts) }}</p>
        </div>
    </div>

    <!-- Chart + Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue — Last 7 Days</h3>
            <div class="relative" style="height: 250px;" x-data="revenueChart()" x-init="init()">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock</h3>
                <span class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 text-xs font-bold px-2 py-1 rounded-full">
                    {{ $lowStock->count() }} items
                </span>
            </div>
            @if($lowStock->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">All stock levels healthy!</p>
                </div>
            @else
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($lowStock as $variant)
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-800">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $variant->product->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $variant->ml_value }}{{ $variant->ml_unit }}@if($variant->type) · {{ $variant->type }}@endif</p>
                        </div>
                        <span class="flex-shrink-0 ml-3 text-sm font-bold {{ $variant->stock <= 0 ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $variant->stock <= 0 ? 'OUT' : $variant->stock . ' left' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All →</a>
        </div>
        @if($recentOrders->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 text-sm py-4 text-center">No orders yet</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-3 px-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Order #</th>
                        <th class="text-left py-3 px-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Customer</th>
                        <th class="text-left py-3 px-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Total</th>
                        <th class="text-left py-3 px-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Status</th>
                        <th class="text-left py-3 px-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="py-3 px-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:underline font-medium">{{ $order->order_number }}</a>
                        </td>
                        <td class="py-3 px-2 text-gray-900 dark:text-white">{{ $order->customer_name }}</td>
                        <td class="py-3 px-2 font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->grand_total) }}</td>
                        <td class="py-3 px-2">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-2 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- AOV + New Customers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Order Value</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳{{ number_format($averageOrderValue, 2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Excluding cancelled orders</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">New Customers</p>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $newCustomers }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Last 7 days</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Top Selling Products
        </h3>
        @if($topProducts->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 text-sm py-4 text-center">No sales data yet</p>
        @else
        <div class="space-y-3">
            @foreach($topProducts as $index => $item)
            <div class="flex items-center gap-4 p-3 rounded-xl {{ $index === 0 ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-amber-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300')) }}">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $item->product->name ?? 'Deleted Product' }}</p>
                    @if($item->product?->brand)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->product->brand->name }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-gray-900 dark:text-white">{{ number_format($item->total_qty) }} sold</p>
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">৳{{ number_format($item->total_revenue) }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
