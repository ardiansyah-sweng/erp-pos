<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function __construct()
    {
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 10 transaksi dengan detail
        for ($i = 1; $i <= 10; $i++) {
            $transaction = Transaction::create([
                'total' => 0,
            ]);

            $total = 0;
            $itemCount = $this->faker->numberBetween(2, 5);

            // Buat detail untuk setiap transaksi
            for ($j = 0; $j < $itemCount; $j++) {
                $quantity = $this->faker->numberBetween(1, 10);
                $price = $this->faker->numberBetween(10000, 500000);
                $amount = $price * $quantity;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => 'P'.str_pad($this->faker->numberBetween(1, 999), 5, '0', STR_PAD_LEFT),
                    'quantity' => $quantity,
                    'price' => $price,
                    'amount' => $amount,
                ]);

                $total += $amount;
            }

            // Update total transaksi
            $transaction->update(['total' => $total]);

            $this->command->info("Transaksi #{$transaction->id} created with total Rp ".number_format($total, 0, ',', '.'));
        }

        $this->command->info('✓ Data transaksi berhasil dibuat!');
    }
}
