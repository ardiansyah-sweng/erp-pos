<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function getTransaction()
    {
        $transactions = Transaction::with('details')
            ->latest()
            ->get();

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

        $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
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

        $transaction = DB::transaction(function () use ($validated, $totalAmount) {
            $transaction = Transaction::create([
                'total' => $totalAmount,
            ]);

            foreach ($validated['items'] as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (int) $item['unit_price'],
                    'amount' => (int) $item['quantity'] * (int) $item['unit_price'],
                ]);
            }

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => [
                'id' => $transaction->id,
                'transaction_number' => 'TRX-'.now()->format('YmdHis').'-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
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

    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric',
            'details' => 'required|array',
        ]);

        $transaction = Transaction::create([
            'total' => $request->total,
        ]);

        foreach ($request->details as $detail) {

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
                'amount' => $detail['quantity'] * $detail['price'],
            ]);
        }

        return response()->json([
            'message' => 'Transaction berhasil ditambahkan',
            'data' => $transaction,
        ], 201);
    }
}
