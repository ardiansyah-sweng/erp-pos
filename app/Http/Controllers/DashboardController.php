<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = (string) $request->query('period', 'hari_ini');

        if (! in_array($period, ['hari_ini', '7_hari', '30_hari', 'kustom'], true)) {
            $period = 'hari_ini';
        }

        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($request, $period);

        $transactionQuery = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'void');

        $summary = [
            'revenue' => (clone $transactionQuery)->sum('total') ?: 0,
            'count' => (clone $transactionQuery)->count(),
            'items' => TransactionDetail::whereHas('transaction',
                fn ($query) => $query->whereBetween('created_at', [$startDate, $endDate])->where('status', '!=', 'void')
            )->sum('quantity') ?: 0,
        ];

        $dates = collect(CarbonPeriod::create(
            $startDate->copy()->startOfDay(),
            $endDate->copy()->startOfDay(),
        ))->map(fn (Carbon $date) => $date->copy());

        $raw = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'void')
            ->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get()
            ->keyBy('tgl');

        $chartLabels = $dates->map(fn ($d) => $d->format('d M'));
        $chartData = $dates->map(fn ($d) => (int) ($raw[$d->format('Y-m-d')]->total ?? 0));

        $topProducts = DB::table('transaction_detail')
            ->join('transaction', 'transaction_detail.transaction_id', '=', 'transaction.id')
            ->join('products', 'transaction_detail.product_id', '=', 'products.id')
            ->selectRaw('
                products.name,
                products.sku,
                SUM(transaction_detail.quantity) as total_qty,
                SUM(transaction_detail.amount) as total_amount
            ')
            ->whereBetween('transaction.created_at', [$startDate, $endDate])
            ->where('transaction.status', '!=', 'void')
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
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($transaction) => [
                'code' => 'TRX-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'time' => $transaction->created_at->format('H:i'),
                'date' => $transaction->created_at->format('d M'),
                'items' => $transaction->details->sum('quantity'),
                'total' => $transaction->total,
                'status' => $transaction->status,
            ]);

        return view('dashboard.index', compact(
            'summary',
            'chartLabels',
            'chartData',
            'topProducts',
            'lowStock',
            'recent',
            'period',
            'periodLabel',
            'startDate',
            'endDate',
        ));
    }

    private function resolvePeriod(Request $request, string $period): array
    {
        return match ($period) {
            '7_hari' => [
                Carbon::today()->subDays(6)->startOfDay(),
                Carbon::today()->endOfDay(),
                '7 hari terakhir',
            ],
            '30_hari' => [
                Carbon::today()->subDays(29)->startOfDay(),
                Carbon::today()->endOfDay(),
                '30 hari terakhir',
            ],
            'kustom' => $this->resolveCustomPeriod($request),
            default => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
                'hari ini',
            ],
        };
    }

    private function resolveCustomPeriod(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        return [
            $startDate,
            $endDate,
            $startDate->translatedFormat('d M Y').' - '.$endDate->translatedFormat('d M Y'),
        ];
    }
}
