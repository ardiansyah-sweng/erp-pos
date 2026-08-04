<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'filter' => ['nullable', Rule::in(['today', 'yesterday', 'this_week', 'this_month', 'custom'])],
            'start_date' => ['nullable', 'required_if:filter,custom', 'date'],
            'end_date' => ['nullable', 'required_if:filter,custom', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['nullable', Rule::in(['cash', 'card', 'e_wallet', 'bank_transfer', 'qris'])],
        ]);

        $filter = $validated['filter'] ?? 'this_month';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $paymentMethod = $validated['payment_method'] ?? null;
        [$start, $end] = $this->getDateRange($filter, $startDate, $endDate);

        $payments = DB::table('payment_details')
            ->join('transaction', 'payment_details.transaction_id', '=', 'transaction.id')
            ->whereBetween('transaction.created_at', [$start, $end])
            ->whereIn('payment_details.payment_status', ['paid', 'success']);

        $methodSummary = (clone $payments)
            ->selectRaw('payment_details.payment_method, COUNT(*) as total_payments, COALESCE(SUM(payment_details.amount), 0) as total_amount')
            ->groupBy('payment_details.payment_method')
            ->orderByDesc('total_amount')
            ->get();

        $methodTotalAmount = (float) $methodSummary->sum('total_amount');
        foreach ($methodSummary as $method) {
            $method->percentage = $methodTotalAmount > 0
                ? round(($method->total_amount / $methodTotalAmount) * 100, 2)
                : 0;
        }

        if ($paymentMethod) {
            $payments->where('payment_details.payment_method', $paymentMethod);
        }

        $summary = (clone $payments)
            ->selectRaw('COUNT(*) as total_payments, COALESCE(SUM(payment_details.amount), 0) as total_amount, COALESCE(AVG(payment_details.amount), 0) as average_amount')
            ->first();

        $dailyPayments = (clone $payments)
            ->selectRaw('DATE(transaction.created_at) as payment_date, COALESCE(SUM(payment_details.amount), 0) as total_amount')
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get();

        $recentPayments = (clone $payments)
            ->select(
                'transaction.id as transaction_id',
                'transaction.created_at as transaction_date',
                'payment_details.payment_method',
                'payment_details.reference_number',
                'payment_details.amount',
            )
            ->orderByDesc('transaction.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('reports.payments', compact(
            'summary',
            'methodSummary',
            'dailyPayments',
            'recentPayments',
            'filter',
            'startDate',
            'endDate',
            'paymentMethod',
            'start',
            'end',
        ));
    }

    private function getDateRange(string $filter, ?string $startDate, ?string $endDate): array
    {
        return match ($filter) {
            'today' => [Carbon::today(), Carbon::now()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'custom' => [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }
}
