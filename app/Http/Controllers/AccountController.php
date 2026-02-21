<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Show the user's account dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Calculate statistics
        $totalOrders = Order::where('user_id', $user->id)->count();

        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');

        // Recent orders
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('account.dashboard', compact(
            'user',
            'totalOrders',
            'totalSpent',
            'recentOrders'
        ));
    }

    /**
     * Show the user's order history.
     */
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('account.orders.index', compact('orders'));
    }

    /**
     * Show the order details.
     */
    public function show(Order $order)
    {
        // Ensure user owns the order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['orderItems.product', 'orderItems.variant']);

        return view('account.orders.show', compact('order'));
    }
}
