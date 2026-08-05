<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    public function memberReport(Request $request)
    {
        $level = $request->get('level', '');

        // ── Summary Cards ──
        $totalMembers = Customer::count();
        $newThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $totalPoints = Customer::sum('points');
        $avgSpending = Customer::join('transaction', 'customers.id', '=', 'transaction.customer_id')
            ->where('transaction.status', '!=', 'void')
            ->avg('transaction.total') ?? 0;

        // ── Distribusi Level (Pie) ──
        $levelDistribution = Customer::selectRaw('member_level, COUNT(*) as total')
            ->groupBy('member_level')
            ->orderBy('member_level')
            ->get();

        // ── Pertumbuhan Member per Bulan (6 bulan) ──
        $monthlyGrowth = Customer::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // ── Top 10 Member by Total Belanja ──
        $topSpenders = Customer::withSum('transactions', 'total')
            ->withCount('transactions')
            ->having('transactions_sum_total', '>', 0)
            ->orderBy('transactions_sum_total', 'desc')
            ->limit(10)
            ->get();

        // ── Top 10 Member by Poin ──
        $topPoints = Customer::orderBy('points', 'desc')
            ->limit(10)
            ->get();

        // ── Top 10 Member by Jumlah Transaksi ──
        $topFrequent = Customer::withCount('transactions')
            ->having('transactions_count', '>', 0)
            ->orderBy('transactions_count', 'desc')
            ->limit(10)
            ->get();

        // ── Tabel semua member ──
        $memberQuery = Customer::withCount('transactions')
            ->withSum('transactions', 'total')
            ->orderBy('transactions_sum_total', 'desc');

        if ($level) {
            $memberQuery->where('member_level', $level);
        }

        $members = $memberQuery->paginate(20)->withQueryString();

        return view('reports.members', compact(
            'totalMembers',
            'newThisMonth',
            'totalPoints',
            'avgSpending',
            'levelDistribution',
            'monthlyGrowth',
            'topSpenders',
            'topPoints',
            'topFrequent',
            'members',
            'level',
        ));
    }

    /**
     * Menampilkan performa penjualan dan kondisi stok produk dari database lokal.
     */
    public function productReport(Request $request)
    {
        $validated = $request->validate([
            'filter' => ['nullable', Rule::in(['today', 'yesterday', 'this_week', 'this_month', 'custom'])],
            'start_date' => ['nullable', 'required_if:filter,custom', 'date'],
            'end_date' => ['nullable', 'required_if:filter,custom', 'date', 'after_or_equal:start_date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'stock_status' => ['nullable', Rule::in(['aman', 'menipis', 'habis'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $filter = $validated['filter'] ?? 'this_month';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $categoryId = $validated['category_id'] ?? null;
        $stockStatus = $validated['stock_status'] ?? null;
        $search = trim($validated['search'] ?? '');

        [$start, $end] = $this->getDateRange($filter, $startDate, $endDate);

        $salesSubquery = DB::table('transaction_detail')
            ->join('transaction', 'transaction_detail.transaction_id', '=', 'transaction.id')
            ->whereBetween('transaction.created_at', [$start, $end])
            ->where('transaction.status', '!=', 'void')
            ->selectRaw('transaction_detail.product_id')
            ->selectRaw('SUM(transaction_detail.quantity) as quantity_sold')
            ->selectRaw('SUM(transaction_detail.amount) as revenue')
            ->selectRaw('COUNT(DISTINCT transaction_detail.transaction_id) as transaction_count')
            ->groupBy('transaction_detail.product_id');

        $productsQuery = Product::query()
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoinSub($salesSubquery, 'sales', function ($join) {
                $join->on('products.id', '=', 'sales.product_id');
            })
            ->select('products.*')
            ->selectRaw('categories.name as category_name')
            ->selectRaw('COALESCE(sales.quantity_sold, 0) as quantity_sold')
            ->selectRaw('COALESCE(sales.revenue, 0) as revenue')
            ->selectRaw('COALESCE(sales.transaction_count, 0) as transaction_count');

        $this->applyProductFilters($productsQuery, $categoryId, $stockStatus, $search);

        $products = $productsQuery
            ->orderByDesc('quantity_sold')
            ->orderBy('products.name')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'min_stock')
                ->count(),
            'out_of_stock_products' => Product::where('stock_quantity', '<=', 0)->count(),
            'quantity_sold' => (int) DB::query()->fromSub($salesSubquery, 'sales_summary')->sum('quantity_sold'),
            'revenue' => (int) DB::query()->fromSub($salesSubquery, 'sales_summary')->sum('revenue'),
        ];

        $topProducts = DB::query()
            ->fromSub($salesSubquery, 'sales_summary')
            ->join('products', 'sales_summary.product_id', '=', 'products.id')
            ->select('products.name', 'products.sku', 'sales_summary.quantity_sold', 'sales_summary.revenue')
            ->orderByDesc('sales_summary.quantity_sold')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::query()
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('reports.products', compact(
            'products',
            'summary',
            'topProducts',
            'lowStockProducts',
            'categories',
            'filter',
            'startDate',
            'endDate',
            'categoryId',
            'stockStatus',
            'search',
            'start',
            'end',
        ));
    }

    /**
     * Hitung rentang tanggal berdasarkan filter
     */
    private function getDateRange($filter, $startDate, $endDate): array
    {
        return match ($filter) {
            'today' => [Carbon::today(), Carbon::now()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'custom' => [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ],
            default => [Carbon::today(), Carbon::now()],
        };
    }

    private function applyProductFilters(
        Builder $query,
        ?int $categoryId,
        ?string $stockStatus,
        string $search,
    ): void {
        $query
            ->when($categoryId, fn (Builder $query) => $query->where('products.category_id', $categoryId))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.sku', 'like', "%{$search}%")
                        ->orWhere('products.barcode', 'like', "%{$search}%");
                });
            })
            ->when($stockStatus === 'habis', fn (Builder $query) => $query->where('products.stock_quantity', '<=', 0))
            ->when($stockStatus === 'menipis', function (Builder $query) {
                $query->where('products.stock_quantity', '>', 0)
                    ->whereColumn('products.stock_quantity', '<=', 'products.min_stock');
            })
            ->when($stockStatus === 'aman', fn (Builder $query) => $query->whereColumn('products.stock_quantity', '>', 'products.min_stock'));
    }

    /**
     * Summary: total transaksi, total pendapatan, rata-rata
     * Kolom: total (sesuai tabel 'transaction')
     */
    private function getSummary($start, $end): array
    {
        $data = Transaction::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'void')
            ->selectRaw('
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total), 0) as total_pendapatan,
                COALESCE(AVG(total), 0) as rata_rata
            ')
            ->first();

        return [
            'total_transaksi' => $data->total_transaksi ?? 0,
            'total_pendapatan' => $data->total_pendapatan ?? 0,
            'rata_rata' => round($data->rata_rata ?? 0),
        ];
    }

    /**
     * Data untuk chart: pendapatan per hari
     */
    private function getChartData($start, $end): array
    {
        $rows = Transaction::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'void')
            ->selectRaw('DATE(created_at) as tanggal, SUM(total) as jumlah')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return [
            'labels' => $rows->pluck('tanggal')->map(fn ($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'data' => $rows->pluck('jumlah')->toArray(),
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
            ->where('transaction.status', '!=', 'void')
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
