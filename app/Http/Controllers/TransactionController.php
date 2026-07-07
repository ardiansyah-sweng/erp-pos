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

class TransactionController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
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
            'packaging_fee' => ['nullable', 'integer', 'min:0'],
            'packaging_name' => ['nullable', 'string', 'max:100'],
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
        $packagingFee = (int) ($validated['packaging_fee'] ?? 0);
        $packagingName = $validated['packaging_name'] ?? 'Tanpa Kemasan';
        $totalAmount = max(0, $subtotal - $discountAmount + $packagingFee);
        $cashTendered = (int) ($validated['cash_tendered'] ?? 0);
        $changeAmount = $validated['payment_method'] === 'cash' ? max(0, $cashTendered - $totalAmount) : 0;
        $paymentStatus = $validated['payment_method'] === 'cash' && $cashTendered < $totalAmount ? 'pending' : 'paid';

        // Build reference_number: encode packaging (and future extras) into the string
        $referenceSegments = [];
        if ($packagingFee > 0) {
            $referenceSegments[] = 'KEMASAN:' . $packagingFee . '|' . $packagingName;
        }
        $referenceNumber = !empty($referenceSegments) ? implode(';', $referenceSegments) : null;

        $transaction = DB::transaction(function () use ($validated, $totalAmount, $discountAmount, $cashTendered, $changeAmount, $paymentStatus, $productQuantities, $referenceNumber) {
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
                'reference_number' => $referenceNumber,
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

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => [
                'id' => $transaction->id,
                'transaction_number' => 'TRX-' . now()->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'packaging_fee' => $packagingFee,
                'packaging_name' => $packagingName,
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

            return response()->json([
                'message' => 'Transaction berhasil ditambahkan',
                'data' => $transaction
            ], 201);
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
