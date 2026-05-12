<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    protected \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactionModel = new Transaction();
        $detailModel = new TransactionDetail();

        for ($i = 0; $i < $this->faker->numberBetween(3, 10); $i++) {
            $transactionDate = Carbon::instance(
                $this->faker->dateTimeBetween('-30 days', 'now')
            )->format('Y-m-d H:i:s');

            $transaction = Transaction::create([
                $transactionModel->getColumn(0) => 0,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ]);

            $total = 0;

            for ($j = 0; $j < $this->faker->numberBetween(1, 5); $j++) {
                $quantity = $this->faker->numberBetween(1, 10);
                $price = $this->faker->numberBetween(5, 100) * 1000;
                $amount = $quantity * $price;

                TransactionDetail::create([
                    $detailModel->getColumn(0) => $transaction->id,
                    $detailModel->getColumn(1) => strtoupper($this->faker->bothify('??####')),
                    $detailModel->getColumn(2) => $quantity,
                    $detailModel->getColumn(3) => $price,
                    $detailModel->getColumn(4) => $amount,
                    $detailModel->getColumn(5) => $transactionDate,
                    $detailModel->getColumn(6) => $transactionDate,
                ]);

                $total += $amount;
            }

            $transaction->update([
                $transactionModel->getColumn(0) => $total,
                'updated_at' => $transactionDate,
            ]);
        }
    }
}
