<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function storeTransaction($items)
    {
        return DB::transaction(function () use ($items) {

            $total = 0;

            foreach ($items as $item) {
                $total += $item['amount'];
            }

            $transactionId = DB::table('transaction')->insertGetId([
                'total' => $total,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($items as $item) {
                DB::table('transaction_detail')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['amount'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return $transactionId;
        });
    }
}