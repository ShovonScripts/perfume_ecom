<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\Request;

class AdminInventoryController extends Controller
{
    /**
     * Display inventory overview with filtering.
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with('product.brand')
            ->where('is_active', true);

        // Stock filters
        if ($request->filled('filter')) {
            if ($request->filter === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<', 5);
            } elseif ($request->filter === 'out') {
                $query->where('stock', 0);
            } elseif ($request->filter === 'healthy') {
                $query->where('stock', '>=', 5);
            }
        }

        // Search by product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Sort
        $sort = $request->get('sort', 'stock_asc');
        if ($sort === 'stock_desc') {
            $query->orderByDesc('stock');
        } elseif ($sort === 'name') {
            $query->join('products', 'product_variants.product_id', '=', 'products.id')
                  ->orderBy('products.name')
                  ->select('product_variants.*');
        } else {
            $query->orderBy('stock');
        }

        $variants = $query->paginate(25)->withQueryString();

        // Summary stats
        $totalVariants = ProductVariant::where('is_active', true)->count();
        $outOfStock = ProductVariant::where('is_active', true)->where('stock', 0)->count();
        $lowStock = ProductVariant::where('is_active', true)->where('stock', '>', 0)->where('stock', '<', 5)->count();
        $totalUnits = ProductVariant::where('is_active', true)->sum('stock');

        // Recent stock logs
        $recentLogs = StockLog::with(['variant.product', 'admin'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.inventory.index', compact(
            'variants', 'totalVariants', 'outOfStock', 'lowStock', 'totalUnits', 'recentLogs'
        ));
    }

    /**
     * Update stock for a variant.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'new_stock' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $oldStock = $variant->stock;
        $newStock = (int) $request->new_stock;

        if ($oldStock === $newStock) {
            return back()->with('error', 'No change in stock value');
        }

        $variant->update(['stock' => $newStock]);

        StockLog::create([
            'product_variant_id' => $variant->id,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'change_amount' => $newStock - $oldStock,
            'reason' => $request->reason ?? 'Manual adjustment',
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Stock updated: ' . $variant->product->name . ' (' . $variant->ml_value . $variant->ml_unit . ') → ' . $newStock);
    }

    /**
     * Export inventory as CSV.
     */
    public function export(Request $request)
    {
        $variants = ProductVariant::with('product.brand')
            ->where('is_active', true)
            ->orderBy('stock')
            ->get();

        $filename = 'inventory-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($variants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product', 'Brand', 'Variant', 'SKU', 'Price', 'Stock', 'Status']);

            foreach ($variants as $v) {
                $status = $v->stock === 0 ? 'Out of Stock' : ($v->stock < 5 ? 'Low Stock' : 'In Stock');
                fputcsv($file, [
                    $v->product->name ?? 'N/A',
                    $v->product->brand->name ?? 'N/A',
                    $v->ml_value . $v->ml_unit,
                    $v->sku ?? '',
                    $v->price,
                    $v->stock,
                    $status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
