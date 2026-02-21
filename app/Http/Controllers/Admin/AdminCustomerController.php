<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    /**
     * Display a listing of customers with order stats.
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'grand_total')
            ->where('is_admin', false);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blocked', false);
            }
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'top_spender') {
            $query->orderByDesc('orders_sum_grand_total');
        } elseif ($sort === 'most_orders') {
            $query->orderByDesc('orders_count');
        } else {
            $query->latest();
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display customer detail with order history.
     */
    public function show(User $user)
    {
        $user->loadCount('orders');
        $user->loadSum('orders', 'grand_total');

        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', compact('user', 'orders'));
    }

    /**
     * Toggle customer block status.
     */
    public function toggleBlock(User $user)
    {
        $user->update([
            'is_blocked' => !$user->is_blocked,
        ]);

        $status = $user->is_blocked ? 'blocked' : 'unblocked';

        return back()->with('success', 'Customer ' . $user->name . ' has been ' . $status);
    }

    /**
     * Update admin note for a customer.
     */
    public function updateNote(Request $request, User $user)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Note updated for ' . $user->name);
    }
}
