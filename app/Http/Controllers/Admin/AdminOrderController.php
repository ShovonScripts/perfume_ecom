<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders with filtering and search.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by order number or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variant']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified order status.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,packed,shipped,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        // Bust dashboard caches
        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.dashboard.chart');

        return back()->with('success', 'Order status updated successfully');
    }

    /**
     * Bulk update order statuses.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'bulk_status' => 'required|in:pending,processing,packed,shipped,delivered,cancelled',
        ]);

        $count = Order::whereIn('id', $request->order_ids)
            ->update(['status' => $request->bulk_status]);

        // Bust dashboard caches
        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.dashboard.chart');

        return back()->with('success', $count . ' orders updated to ' . ucfirst($request->bulk_status));
    }

    /**
     * Export orders as CSV.
     */
    public function export(Request $request)
    {
        $query = Order::query();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->latest()->get();

        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order #', 'Customer', 'Phone', 'Email', 'City', 'Area',
                'Subtotal', 'Discount', 'Shipping', 'Grand Total',
                'Status', 'Payment Method', 'Date'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->name,
                    $order->phone,
                    $order->email ?? '',
                    $order->city,
                    $order->area,
                    $order->subtotal,
                    $order->discount,
                    $order->shipping_fee,
                    $order->grand_total,
                    ucfirst($order->status),
                    $order->payment_method ?? 'COD',
                    $order->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download invoice PDF for the order.
     */
    public function invoice(Order $order)
    {
        $order->load(['items.product', 'items.variant']);

        $pdf = Pdf::loadView('admin.orders.invoice', [
            'order' => $order
        ]);

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Verify the payment for the order.
     */
    public function verifyPayment(Order $order)
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        // Bust dashboard caches
        Cache::forget('admin.dashboard.stats');
        Cache::forget('admin.dashboard.chart');

        return back()->with('success', 'Payment verified and order marked as processing.');
    }
}
