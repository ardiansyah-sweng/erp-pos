<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function getTransaction(Request $request)
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

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,e_wallet,bank_transfer'],
            'cash_tendered' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
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
        $cashTendered = (int) ($validated['cash_tendered'] ?? 0);
        $changeAmount = $validated['payment_method'] === 'cash' ? max(0, $cashTendered - $totalAmount) : 0;
        $paymentStatus = $validated['payment_method'] === 'cash' && $cashTendered < $totalAmount ? 'pending' : 'paid';

        $transaction = DB::transaction(function () use ($validated, $totalAmount, $discountAmount, $cashTendered, $changeAmount, $paymentStatus, $productQuantities) {
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
                'total' => $totalAmount,
            ]);

            $transaction->payments()->create([
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'discount_amount' => $discountAmount,
                'cash_tendered' => $cashTendered,
                'change_amount' => $changeAmount,
            ]);

            foreach ($validated['items'] as $item) {

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (int) $item['unit_price'],
                    'amount' => (int) $item['quantity'] * (int) $item['unit_price'],
                ]);

                Product::where('id', $item['product_id'])
                    ->decrement(
                        'stock_quantity',
                        (int) $item['quantity']
                    );
            }

            foreach ($productQuantities as $productId => $quantity) {
                $lockedProducts->get((int) $productId)->decrement('stock_quantity', (int) $quantity);
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
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'cash_tendered' => $cashTendered,
                'change_amount' => $changeAmount,
                'details' => $detailRows,
            ],
        ], 201);
    }

        public function downloadPdf($id)
        {
            $transaction = Transaction::with('details.product')->findOrFail($id);

            $data = [
                'transaction' => $transaction,
                'transaction_number' => 'TRX-' . $transaction->created_at->format('YmdHis') . '-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT),
                'created_at' => $transaction->created_at,
                'details' => $transaction->details,
            ];

            $pdf = Pdf::loadView('pdf.invoice', $data);

            return $pdf->stream('invoice-' . $transaction->id . '.pdf');
        }

        public function index()
        {
            $transactions = Transaction::with('details')
                ->latest()
                ->get();

            $totalTransactions = Transaction::count();
            $totalRevenue = Transaction::sum('total');
            $totalItemsSold = TransactionDetail::sum('quantity');

            return view('transactions.index', compact(
                'transactions',
                'totalTransactions',
                'totalRevenue',
                'totalItemsSold'
            ));
        }

        public function store(Request $request)
        {
            $request->validate([
                'total' => 'required|numeric',
                'details' => 'required|array'
            ]);

            $transaction = Transaction::create([
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

}