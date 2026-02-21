<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ── Cached dashboard stats (5 minutes) ──────────────
        $stats = Cache::remember('admin.dashboard.stats', 300, function () use ($today) {
            return [
                'todayOrders'    => Order::whereDate('created_at', $today)->count(),
                'todayRevenue'   => Order::whereDate('created_at', $today)
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total'),
                'pendingOrders'  => Order::where('status', 'pending')->count(),
                'processingOrders' => Order::where('status', 'processing')->count(),
                'totalOrders'    => Order::count(),
                'totalRevenue'   => Order::where('status', '!=', 'cancelled')
                    ->sum('grand_total'),
                'monthRevenue'   => Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total'),
                'totalProducts'  => Product::where('is_active', true)->count(),
                'newCustomers'   => User::where('created_at', '>=', now()->subDays(7))->count(),
                'averageOrderValue' => Order::where('status', '!=', 'cancelled')->avg('grand_total') ?? 0,
            ];
        });

        // Extract stats
        $todayOrders = $stats['todayOrders'];
        $todayRevenue = $stats['todayRevenue'];
        $pendingOrders = $stats['pendingOrders'];
        $processingOrders = $stats['processingOrders'];
        $totalOrders = $stats['totalOrders'];
        $totalRevenue = $stats['totalRevenue'];
        $monthRevenue = $stats['monthRevenue'];
        $totalProducts = $stats['totalProducts'];
        $newCustomers = $stats['newCustomers'];
        $averageOrderValue = $stats['averageOrderValue'];

        // Low stock variants (not cached — real-time accuracy needed)
        $lowStock = ProductVariant::with('product')
            ->whereHas('product', function($q) {
                $q->where('is_active', true);
            })
            ->where('stock', '<', 5)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // ── Cached chart data (5 minutes) ───────────────────
        $last7Days = Cache::remember('admin.dashboard.chart', 300, function () {
            $startDate = now()->subDays(6)->startOfDay();
            $chartData = Order::where('created_at', '>=', $startDate)
                ->selectRaw("DATE(created_at) as date, 
                             SUM(CASE WHEN status != 'cancelled' THEN grand_total ELSE 0 END) as revenue,
                             COUNT(*) as orders")
                ->groupByRaw('DATE(created_at)')
                ->pluck('revenue', 'date')
                ->toArray();

            $chartOrders = Order::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders')
                ->groupByRaw('DATE(created_at)')
                ->pluck('orders', 'date')
                ->toArray();

            $days = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $days->push([
                    'date'    => now()->subDays($i)->format('M d'),
                    'revenue' => (float) ($chartData[$date] ?? 0),
                    'orders'  => (int) ($chartOrders[$date] ?? 0),
                ]);
            }
            return $days;
        });

        // Recent orders (not cached — must be real-time)
        $recentOrders = Order::latest()->take(5)->get();

        // ── Cached top products (10 minutes) ────────────────
        $topProducts = Cache::remember('admin.dashboard.top_products', 600, function () {
            return OrderItem::select(
                    'product_id',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(line_total) as total_revenue')
                )
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->with('product.brand')
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'pendingOrders',
            'processingOrders',
            'totalOrders',
            'totalRevenue',
            'monthRevenue',
            'totalProducts',
            'lowStock',
            'last7Days',
            'recentOrders',
            'topProducts',
            'newCustomers',
            'averageOrderValue'
        ));
    }
}
