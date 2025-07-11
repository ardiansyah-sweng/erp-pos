<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Http;
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
        $products = $this->getProduct();
        $tableTransactionDetail = new TransactionDetail();

        # Iterasi jumlah transaksi
        for ($i = 1; $i <= $this->faker->numberBetween(1, 10); $i++) {
            $total = 0;
            foreach ($products as $product) {
                # Dapatkan avg_base_price berdasarkan product_id
                $data = $this->getProductPrices($product['product_id']);
    
                // Pastikan $data tidak null
                if ($data) {
                    $quantity = $this->faker->numberBetween(1, 10);
                    $amount = $data['now_avg_base_price'] * $quantity;
    
                    $created_at = Carbon::parse($data['created_at'])->format('Y-m-d H:i:s');
                    $transactionDate = $this->faker->dateTimeBetween($created_at, 'now')->format('Y-m-d H:i:s');
    
                    TransactionDetail::create([
                        $tableTransactionDetail->getColumn(0) => $i,
                        $tableTransactionDetail->getColumn(1) => $product['product_id'],
                        $tableTransactionDetail->getColumn(2) => $quantity,
                        $tableTransactionDetail->getColumn(3) => $data['now_avg_base_price'],
                        $tableTransactionDetail->getColumn(4) => $amount,
                        $tableTransactionDetail->getColumn(5) => $transactionDate,
                        $tableTransactionDetail->getColumn(6) => $transactionDate
                    ]);
    
                    $total += $amount;
                } else {
                    // Jika $data null, log error atau beri pesan sesuai kebutuhan
                    echo "Harga produk dengan ID {$product['product_id']} tidak ditemukan.\n";
                }
            }
            
            
            Transaction::create([
                (new Transaction())->getColumn(0) => $total,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate
            ]);
        }
    }
    
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
