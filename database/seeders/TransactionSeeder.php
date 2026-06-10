<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;

use Carbon\Carbon;
use Faker\Factory as Faker;

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
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        $transactionDates = [
            Carbon::now()->setTime(15, 10),
            Carbon::now()->subDay()->setTime(10, 30),
            Carbon::now()->subDays(2)->setTime(19, 15),
            Carbon::now()->subDays(5)->setTime(8, 45),
            Carbon::now()->subDays(10)->setTime(13, 5),
        ];

        foreach ($transactionDates as $timestamp) {
            $selectedProducts = $products->random($this->faker->numberBetween(1, min(4, $products->count())));
            $transaction = Transaction::create([
                'total' => 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
            $total = 0;

            foreach ($selectedProducts as $product) {
                $quantity = $this->faker->numberBetween(1, 5);
                $amount = $product->selling_price * $quantity;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $product->id,
                    'quantity' => $quantity,
                    'price' => $product->selling_price,
                    'amount' => $amount,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $total += $amount;
            }

            $paymentMethod = $this->faker->randomElement(['cash', 'card', 'e_wallet', 'bank_transfer']);
            $cashTendered = $paymentMethod === 'cash' ? $total + $this->faker->randomElement([0, 5000, 10000, 20000]) : 0;

            $transaction->update([
                'total' => $total,
                'payment_method' => $paymentMethod,
                'discount_amount' => 0,
                'cash_tendered' => $cashTendered,
                'change_amount' => $paymentMethod === 'cash' ? max(0, $cashTendered - $total) : 0,
            ]);
        }
    }
}
