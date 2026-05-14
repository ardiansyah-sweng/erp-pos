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

        for ($index = 0; $index < $this->faker->numberBetween(3, 8); $index++) {
            $selectedProducts = $products->random($this->faker->numberBetween(1, min(4, $products->count())));
            $transaction = Transaction::create(['total' => 0]);
            $total = 0;

            foreach ($selectedProducts as $product) {
                $quantity = $this->faker->numberBetween(1, 5);
                $amount = $product->selling_price * $quantity;
                $timestamp = Carbon::now()->subDays($this->faker->numberBetween(0, 14));

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

            $transaction->update([
                'total' => $total,
            ]);
        }
    }
<<<<<<< HEAD
    
    function getProductPrices($productID)
    {
        $prices = Http::get('http://127.0.0.1:8000/prices')->json();
    
        $ids = [];
        foreach ($prices as $price) {
            if ($price['product_id'] == $productID) {
                $ids[] = $price['id'];
            }
        }
    
        // Pastikan $ids tidak kosong sebelum mencoba mengaksesnya
        if ($ids) {
            shuffle($ids);
            $selectedID = $ids[0];
    
            foreach ($prices as $price) {
                if ($price['id'] == $selectedID) {
                    print_r($price['now_avg_base_price']);
                    echo "\n";
                    return $price;
                }
            }
        }
    
        // Jika tidak ada yang ditemukan, kembalikan null
        return null;
    }
            
    function getProduct()
    {
        $products = Http::get('http://127.0.0.1:8000/products')->json();
        $keys = array_keys($products);
        shuffle($keys);

        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[$key] = $products[$key];
        }
        
        return array_slice($shuffled, 0, $this->faker->numberBetween(1, count($shuffled)));
    }
}
=======
}
>>>>>>> origin/develop
