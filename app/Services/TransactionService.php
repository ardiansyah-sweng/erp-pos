<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function store(array $payload): Transaction
    {
        $items = collect($payload['items'])->map(function (array $item) {
            return [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (int) $item['unit_price'],
                'amount' => (int) $item['quantity'] * (int) $item['unit_price'],
            ];
        });

        $productIds = $items->pluck('product_id')->unique()->values();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw new \InvalidArgumentException('Some products were not found.');
        }

        $discountAmount = (int) ($payload['discount_amount'] ?? 0);
        $totalAmount = $this->calculateTotal($items->toArray(), $discountAmount);

        return DB::transaction(function () use ($items, $totalAmount) {
            $transaction = Transaction::create([
                'total' => $totalAmount,
                'transaction_date' => now(),
            ]);

            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'],
                    'amount' => $item['amount'],
                ]);
            }

            return $transaction->load('details');
        });
    }

    public function calculateTotal(array $items, int $discountAmount = 0): int
    {
        $subtotal = array_sum(array_map(function (array $item) {
            return $item['quantity'] * $item['unit_price'];
        }, $items));

        return max(0, $subtotal - $discountAmount);
    }
}
