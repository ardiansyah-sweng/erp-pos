<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\SyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturnTransactionController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $transactions = Transaction::with(['details.product', 'details.returnDetails', 'payments'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', preg_replace('/\D/', '', $search) ?: 0);
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $returns = ReturnTransaction::with(['transaction', 'details.product'])
            ->latest()
            ->limit(10)
            ->get();

        return view('returns.index', compact('transactions', 'returns', 'search'));
    }

    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $items = collect($validated['items'])
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn ($quantity) => $quantity > 0);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu item yang akan diretur.',
            ]);
        }

        $returnTransaction = DB::transaction(function () use ($transaction, $validated, $items) {
            $details = TransactionDetail::query()
                ->with('returnDetails')
                ->where('transaction_id', $transaction->id)
                ->whereIn('id', $items->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalRefund = 0;
            $returnRows = [];

            foreach ($items as $detailId => $quantity) {
                $detail = $details->get((int) $detailId);

                if (! $detail) {
                    throw ValidationException::withMessages([
                        'items' => 'Item transaksi tidak ditemukan.',
                    ]);
                }

                $returnedQuantity = $detail->returnDetails->sum('quantity');
                $availableQuantity = max(0, (int) $detail->quantity - (int) $returnedQuantity);

                if ($quantity > $availableQuantity) {
                    throw ValidationException::withMessages([
                        "items.{$detailId}" => "Qty retur melebihi sisa item. Sisa dapat diretur: {$availableQuantity}.",
                    ]);
                }

                $amount = $quantity * (int) $detail->price;
                $totalRefund += $amount;

                $returnRows[] = [
                    'transaction_detail_id' => $detail->id,
                    'product_id' => (int) $detail->product_id,
                    'quantity' => $quantity,
                    'price' => (int) $detail->price,
                    'amount' => $amount,
                ];
            }

            $returnTransaction = ReturnTransaction::create([
                'transaction_id' => $transaction->id,
                'return_code' => $this->buildReturnCode($transaction),
                'reason' => $validated['reason'] ?? null,
                'total_refund' => $totalRefund,
            ]);

            foreach ($returnRows as $row) {
                $returnTransaction->details()->create($row);

                Product::query()
                    ->where('id', $row['product_id'])
                    ->increment('stock_quantity', $row['quantity']);
            }

            return $returnTransaction;
        });

        $this->syncService->log(
            'Return',
            'Berhasil',
            "CREATE - Retur transaksi berhasil diproses dengan kode: {$returnTransaction->return_code}"
        );

        return redirect()
            ->route('returns.index')
            ->with('success', "Retur {$returnTransaction->return_code} berhasil disimpan.");
    }

    private function buildReturnCode(Transaction $transaction): string
    {
        return 'RTR-' . now()->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
    }
}