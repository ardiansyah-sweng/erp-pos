<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LowStockReportController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = Product::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            });

        $summary = [
            'total' => (clone $query)->whereColumn('stock_quantity', '<=', 'min_stock')->count(),
            'empty' => (clone $query)->where('stock_quantity', '<=', 0)->count(),
            'low' => (clone $query)
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'min_stock')
                ->count(),
            'all' => (clone $query)->count(),
        ];

        $products = $query
            ->orderByRaw('CASE WHEN stock_quantity <= 0 THEN 0 WHEN stock_quantity <= min_stock THEN 1 ELSE 2 END')
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('stock-reports.low-stock', compact('products', 'summary', 'search'));
    }
}
