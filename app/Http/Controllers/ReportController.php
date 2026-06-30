<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan penjualan
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($filter, $startDate, $endDate);

        // Summary cards
        $summary = $this->getSummary($start, $end);

        // Data chart harian
        $chartData = $this->getChartData($start, $end);

        // Tabel transaksi
        $transactions = Transaction::whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        // Top produk terlaris
        $topProducts = $this->getTopProducts($start, $end);

        return view('reports.index', compact(
            'summary',
            'chartData',
            'transactions',
            'topProducts',
            'filter',
            'startDate',
            'endDate',
            'start',
            'end'
        ));
    }

    /**
     * Hitung rentang tanggal berdasarkan filter
     */
    private function getDateRange($filter, $startDate, $endDate): array
    {
        return match ($filter) {
            'today'      => [Carbon::today(), Carbon::now()],
            'yesterday'  => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'this_week'  => [Carbon::now()->startOfWeek(), Carbon::now()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'custom'     => [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ],
            default => [Carbon::today(), Carbon::now()],
        };
    }

    /**
     * Summary: total transaksi, total pendapatan, rata-rata
     * Kolom: total (sesuai tabel 'transaction')
     */
    private function getSummary($start, $end): array
    {
        $data = Transaction::whereBetween('created_at', [$start, $end])
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total), 0) as total_pendapatan,
                COALESCE(AVG(total), 0) as rata_rata
            ')
            ->first();

        return [
            'total_transaksi'  => $data->total_transaksi ?? 0,
            'total_pendapatan' => $data->total_pendapatan ?? 0,
            'rata_rata'        => round($data->rata_rata ?? 0),
        ];
    }

    /**
     * Data untuk chart: pendapatan per hari
     */
    private function getChartData($start, $end): array
    {
        $rows = Transaction::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as tanggal, SUM(total) as jumlah')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return [
            'labels' => $rows->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'data'   => $rows->pluck('jumlah')->toArray(),
        ];
    }

    /**
     * Top 5 produk terlaris dalam rentang waktu
     * Tabel: transaction_detail (tanpa 's')
     */
    private function getTopProducts($start, $end): \Illuminate\Support\Collection
    {
        return DB::table('transaction_detail')
            ->join('transaction', 'transaction_detail.transaction_id', '=', 'transaction.id')
            ->join('products', 'transaction_detail.product_id', '=', 'products.id')
            ->whereBetween('transaction.created_at', [$start, $end])
            ->selectRaw('
                products.name as nama_produk,
                SUM(transaction_detail.quantity) as total_qty,
                SUM(transaction_detail.amount) as total_penjualan
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }
}