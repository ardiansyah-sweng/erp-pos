<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStart = Carbon::today();
        $todayEnd   = Carbon::today()->endOfDay();

        $todayQuery = Transaction::whereBetween('created_at', [$todayStart, $todayEnd]);

        $summary = [
            'revenue' => $todayQuery->sum('total') ?: 0,
            'count'   => $todayQuery->count(),
            'items'   => TransactionDetail::whereHas('transaction',
                fn($q) => $q->whereBetween('created_at', [$todayStart, $todayEnd])
            )->sum('quantity') ?: 0,
        ];

        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i));
        }

        $raw = Transaction::whereBetween('created_at', [$dates->first()->startOfDay(), $todayEnd])
            ->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get()
            ->keyBy('tgl');

        $chartLabels = $dates->map(fn($d) => $d->format('d M'));
        $chartData   = $dates->map(fn($d) => (int) ($raw[$d->format('Y-m-d')]->total ?? 0));

        $topProducts = DB::table('transaction_detail')
            ->join('products', 'transaction_detail.product_id', '=', 'products.id')
            ->selectRaw('
                products.name,
                products.sku,
                SUM(transaction_detail.quantity) as total_qty,
                SUM(transaction_detail.amount) as total_amount
            ')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get();

        $recent = Transaction::with('details')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'code'  => 'TRX-' . str_pad((string) $t->id, 4, '0', STR_PAD_LEFT),
                'time'  => $t->created_at->format('H:i'),
                'date'  => $t->created_at->format('d M'),
                'items' => $t->details->sum('quantity'),
                'total' => $t->total,
            ]);

        return view('dashboard.index', compact(
            'summary',
            'chartLabels',
            'chartData',
            'topProducts',
            'lowStock',
            'recent'
        ));
    }
}
