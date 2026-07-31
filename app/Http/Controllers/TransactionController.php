<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\StockAdjustmentService;
use App\Services\SyncService;

class TransactionController extends Controller
{
    protected $customerService;
    protected $syncService;
    protected $stockAdjustmentService;

    public function __construct(CustomerService $customerService, SyncService $syncService, StockAdjustmentService $stockAdjustmentService)
    {
        $this->customerService = $customerService;
        $this->syncService = $syncService;
        $this->stockAdjustmentService = $stockAdjustmentService;
    }

    public function getTransaction(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        // Whitelist eksplisit opsi sort -> kolom asli, supaya nilai dari
        // query string tidak pernah dipakai langsung sebagai nama kolom.
        $sortColumns = [
            'date' => 'created_at',
            'id' => 'id',
            'total' => 'total',
            'items' => 'items_total_quantity',
        ];

        $sortParam = (string) $request->query('sort', 'date');
        $sort = array_key_exists($sortParam, $sortColumns) ? $sortParam : 'date';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $transactions = Transaction::with(['details.product', 'details.returnDetails', 'payments'])
            ->withSum('details as items_total_quantity', 'quantity')
            ->when($validated['date'] ?? null, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->orderBy($sortColumns[$sort], $direction)
            ->get();

        if (!$request->expectsJson() && !$request->ajax()) {
            return view('transactions.index', [
                'transactions' => $transactions,
                'selectedDate' => $validated['date'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $transactions = Transaction::with(['details.product', 'payments'])
            ->when($validated['date'] ?? null, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->get();

        $fileName = 'detail-transaksi';

        if (!empty($validated['date'])) {
            $fileName .= '-' . $validated['date'];
        }

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            echo "\xEF\xBB\xBF";

            fputcsv($handle, [
                'Kode Transaksi',
                'Status',
                'Tanggal',
                'Jam',
                'Metode Pembayaran',
                'SKU Produk',
                'Nama Produk',
                'Qty',
                'Harga Satuan',
                'Subtotal Item',
                'Diskon Item',
                'Total Item Setelah Diskon',
                'Cash Transaksi',
                'Kembalian Transaksi',
            ]);

            foreach ($transactions as $transaction) {
                $payment = $transaction->payments->first();
                $transactionCode = 'TRX-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
                $subtotal = $transaction->details->sum('amount');
                $discount = min((int) ($payment?->discount_amount ?? 0), (int) $subtotal);
                $allocatedDiscount = 0;
                $lastDetailIndex = max(0, $transaction->details->count() - 1);

                foreach ($transaction->details->values() as $index => $detail) {
                    $itemSubtotal = (int) $detail->amount;
                    $itemDiscount = 0;

                    if ($subtotal > 0 && $discount > 0) {
                        $itemDiscount = $index === $lastDetailIndex
                            ? $discount - $allocatedDiscount
                            : (int) floor(($itemSubtotal / $subtotal) * $discount);
                    }

                    $allocatedDiscount += $itemDiscount;

                    fputcsv($handle, [
                        $transactionCode,
                        $transaction->status === 'void' ? 'Dibatalkan' : 'Selesai',
                        $transaction->created_at?->format('d/m/Y'),
                        $transaction->created_at?->format('H:i'),
                        $payment?->payment_method ?? 'cash',
                        $detail->product?->sku ?? '-',
                        $detail->product?->name ?? 'Produk #' . $detail->product_id,
                        $detail->quantity,
                        $detail->price,
                        $itemSubtotal,
                        $itemDiscount,
                        max(0, $itemSubtotal - $itemDiscount),
                        $payment?->cash_tendered ?? 0,
                        $payment?->change_amount ?? 0,
                    ]);
                }
            }

            fclose($handle);
        }, $fileName . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable','integer','exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,e_wallet,bank_transfer,qris'],
            'cash_tendered' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'parking_fee' => ['nullable', 'integer', 'min:0', 'in:0,2000,5000'],
        ]);

        $items = collect($validated['items']);
        $productIds = $items->pluck('product_id')->unique()->values();
        $productQuantities = $items->groupBy('product_id')->map(function ($productItems) {
            return $productItems->sum(fn ($item) => (int) $item['quantity']);
        });
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $subtotal = 0;
        $detailRows = [];

        foreach ($validated['items'] as $item) {
            $product = $products->get($item['product_id']);
            $quantity = (int) $item['quantity'];
            $unitPrice = (int) $item['unit_price'];
            $lineTotal = $quantity * $unitPrice;

            $subtotal += $lineTotal;

            $detailRows[] = [
                'product' => [
                    'id' => $product?->id,
                    'name' => $product?->name,
                    'sku' => $product?->sku,
                ],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
            ];
        }

        $discountAmount = (int) ($validated['discount_amount'] ?? 0);
        $totalAmount = max(0, $subtotal - $discountAmount);
        $parkingFee = (int) ($validated['parking_fee'] ?? 0);
        $totalAmount += $parkingFee;
        $parkingType = match ($parkingFee) {
            2000 => 'motor',
            5000 => 'mobil',
            default => 'none',
        };
        $cashTendered = (int) ($validated['cash_tendered'] ?? 0);
        $changeAmount = $validated['payment_method'] === 'cash' ? max(0, $cashTendered - $totalAmount) : 0;
        $paymentStatus = $validated['payment_method'] === 'cash' && $cashTendered < $totalAmount ? 'pending' : 'paid';

        $transaction = DB::transaction(function () use ($validated, $totalAmount, $discountAmount, $cashTendered, $changeAmount, $paymentStatus, $productQuantities, $parkingFee, $parkingType) {
            $lockedProducts = Product::query()
                ->whereIn('id', $productQuantities->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($productQuantities as $productId => $quantity) {
                $product = $lockedProducts->get((int) $productId);

                if (!$product || $product->stock_quantity < $quantity) {
                    $productName = $product?->name ?? 'produk';
                    $remainingStock = $product?->stock_quantity ?? 0;

                    throw ValidationException::withMessages([
                        'items' => "Stok {$productName} tidak cukup. Sisa stok {$remainingStock}.",
                    ]);
                }
            }

            $transaction = Transaction::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'total' => $totalAmount,
            ]);

            $transaction->payments()->create([
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'discount_amount' => $discountAmount,
                'cash_tendered' => $cashTendered,
                'change_amount' => $changeAmount,
                'parking_fee' => $parkingFee,
                'parking_type' => $parkingType,
            ]);

            foreach ($validated['items'] as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (int) $item['unit_price'],
                    'amount' => (int) $item['quantity'] * (int) $item['unit_price'],
                ]);

                $product = Product::find((int) $item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup");
                }

                $product->stock_quantity -= $item['quantity'];
                $product->save();
    
            }

            foreach ($productQuantities as $productId => $quantity) {
                $lockedProducts->get((int) $productId)->decrement('stock_quantity', (int) $quantity);
            }

            if (!empty($validated['customer_id'])) {

                $customer = Customer::find($validated['customer_id']);

                if ($customer) {
                    $this->customerService->addPoints($customer, $totalAmount);
                }

            }

            return $transaction;
        });

        $this->syncService->log(
            'Transaction',
            'Berhasil',
            "CREATE - Transaksi berhasil dibuat. ID: {$transaction->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => [
                'id' => $transaction->id,
                'transaction_number' => 'TRX-' . now()->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'parking_fee' => $parkingFee,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'cash_tendered' => $cashTendered,
                'change_amount' => $changeAmount,
                'details' => $detailRows,
            ],
        ], 201);
    }

    public function store(Request $request)
    {
            $request->validate([
                'total' => 'required|numeric',
                'details' => 'required|array'
            ]);

            $transaction = Transaction::create([
                'customer_id' => $request->customer_id,
                'total' => $request->total
            ]);

            $transaction->payments()->create([
                'payment_method' => 'cash',
                'amount' => $request->total,
            ]);

            foreach ($request->details as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'amount' => $detail['quantity'] * $detail['price']
                ]);
            }

            $this->syncService->log(
                'Transaction',
                'Berhasil',
                "CREATE - Transaksi berhasil dibuat. ID: {$transaction->id}"
            );

            return response()->json([
                'message' => 'Transaction berhasil ditambahkan',
                'data' => $transaction
            ], 201);
        }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->isVoided()) {
            throw ValidationException::withMessages([
                'transaction' => 'Transaksi yang sudah dibatalkan tidak dapat diedit.',
            ]);
        }

        if ($transaction->returns()->exists()) {
            throw ValidationException::withMessages([
                'transaction' => 'Transaksi yang sudah memiliki retur tidak dapat diedit.',
            ]);
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.detail_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
        ]);

        $transaction = DB::transaction(function () use ($request, $transaction, $validated) {
            $details = TransactionDetail::query()
                ->where('transaction_id', $transaction->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $remainingLines = collect($validated['items'])->filter(fn ($item) => (int) $item['quantity'] > 0);

            if ($remainingLines->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Transaksi harus memiliki minimal satu item. Gunakan void untuk membatalkan seluruh transaksi.',
                ]);
            }

            $lockedProducts = Product::query()
                ->whereIn('id', $details->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($validated['items'] as $item) {
                $detail = $details->get($item['detail_id']);

                if (!$detail) {
                    throw ValidationException::withMessages([
                        'items' => 'Item transaksi tidak ditemukan.',
                    ]);
                }

                $newQuantity = (int) $item['quantity'];
                $delta = $newQuantity - (int) $detail->quantity;
                $product = $lockedProducts->get((int) $detail->product_id);

                if ($delta > 0 && (!$product || $product->stock_quantity < $delta)) {
                    $remaining = $product?->stock_quantity ?? 0;
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product?->name} tidak cukup untuk menambah qty. Sisa stok {$remaining}.",
                    ]);
                }

                if ($product) {
                    $product->stock_quantity -= $delta;
                    $product->save();
                }

                if ($newQuantity === 0) {
                    $detail->delete();
                } else {
                    $detail->update([
                        'quantity' => $newQuantity,
                        'amount' => $newQuantity * $detail->price,
                    ]);
                }
            }

            $subtotal = (int) TransactionDetail::where('transaction_id', $transaction->id)->sum('amount');
            $payment = $transaction->payments()->first();
            $discountAmount = (int) ($payment?->discount_amount ?? 0);
            $parkingFee = (int) ($payment?->parking_fee ?? 0);
            $newTotal = max(0, $subtotal - $discountAmount) + $parkingFee;

            $transaction->update([
                'customer_id' => array_key_exists('customer_id', $validated) ? $validated['customer_id'] : $transaction->customer_id,
                'total' => $newTotal,
            ]);

            if ($payment) {
                $cashTendered = (int) $payment->cash_tendered;
                $changeAmount = $payment->payment_method === 'cash' ? max(0, $cashTendered - $newTotal) : 0;

                $payment->update([
                    'amount' => $newTotal,
                    'change_amount' => $changeAmount,
                ]);
            }

            return $transaction->fresh(['details.product', 'payments']);
        });

        $this->syncService->log(
            'Transaction',
            'Berhasil',
            "UPDATE - Transaksi berhasil diperbarui. ID: {$transaction->id}"
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => $transaction,
            ]);
        }

        return back()->with('success', "Transaksi #{$transaction->id} berhasil diperbarui.");
    }

    public function void(Request $request, Transaction $transaction)
    {
        if (!$transaction->canBeVoided()) {
            throw ValidationException::withMessages([
                'transaction' => $transaction->isVoided()
                    ? 'Transaksi ini sudah dibatalkan sebelumnya.'
                    : 'Transaksi yang sudah memiliki retur tidak dapat dibatalkan.',
            ]);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $transactionCode = 'TRX-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($transaction, $validated, $transactionCode) {
            foreach ($transaction->details as $detail) {
                if ($detail->product) {
                    $this->stockAdjustmentService->adjustStock(
                        $detail->product,
                        (int) $detail->quantity,
                        'in',
                        "Void transaksi {$transactionCode}",
                    );
                }
            }

            $transaction->update([
                'status' => 'void',
                'void_reason' => $validated['reason'] ?? null,
                'voided_at' => now(),
            ]);

            $transaction->payments()->update(['payment_status' => 'void']);
        });

        $this->syncService->log(
            'Transaction',
            'Berhasil',
            "VOID - Transaksi dibatalkan. ID: {$transaction->id}. Alasan: " . ($validated['reason'] ?? '-')
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Transaksi {$transactionCode} berhasil dibatalkan.",
                'data' => $transaction->fresh(),
            ]);
        }

        return back()->with('success', "Transaksi {$transactionCode} berhasil dibatalkan.");
    }

    public function salesNotes(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transactions = $this->buildSalesData($startDate, $endDate);
        $isPdf = false;

        return view('transactions.sales-notes', compact('transactions', 'startDate', 'endDate', 'isPdf'));
    }

    public function downloadSalesReportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $transactions = $this->buildSalesData($startDate, $endDate);
        $isPdf = true;

        $fileName = 'laporan-penjualan';
        if ($startDate && $endDate) {
            $fileName .= '-' . $startDate . '-to-' . $endDate;
        } elseif ($startDate) {
            $fileName .= '-dari-' . $startDate;
        } elseif ($endDate) {
            $fileName .= '-sampai-' . $endDate;
        }

        $pdf = Pdf::loadView('transactions.sales-notes', compact('transactions', 'startDate', 'endDate', 'isPdf'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($fileName . '.pdf');
    }

    private function buildSalesData($startDate = null, $endDate = null)
    {
        $query = Transaction::with(['details.product'])->latest();

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->get()->map(function ($transaction) {
            return [
                'transaction_code' => 'TRX-' . $transaction->created_at->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'date' => $transaction->created_at,
                'item_count' => $transaction->details->sum('quantity'),
                'total' => $transaction->total,
                'details' => $transaction->details,
            ];
        });
    }
}
