<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        // Menggunakan locale Indonesia agar data faker lebih relevan
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = $this->getProduct();
        
        // Pastikan ada produk sebelum lanjut, biar gak error foreach
        if (empty($products)) {
            $this->command->warn("Tidak ada data di tabel products! Isi dulu lewat Tinker.");
            return;
        }

        $tableTransactionDetail = new TransactionDetail();

        # Iterasi jumlah transaksi (membuat 1 sampai 10 transaksi)
        for ($i = 1; $i <= $this->faker->numberBetween(1, 10); $i++) {
            $total = 0;
            $transactionDate = now()->toDateTimeString(); // Default waktu sekarang

            foreach ($products as $product) {
                # Dapatkan harga produk (tanpa HTTP request)
                $data = $this->getProductPrices($product['product_id']);
    
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
                }
            }
            
            # Buat header transaksi setelah detailnya masuk
            Transaction::create([
                (new Transaction())->getColumn(0) => $total,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate
            ]);
        }
    }
    
    /**
     * Pengganti Http::get('.../prices')
     */
    function getProductPrices($productID)
    {
        // Langsung generate harga acak agar tidak timeout
        return [
            'now_avg_base_price' => $this->faker->numberBetween(1000, 50000),
            'created_at' => now()->subDays(10)->toDateTimeString()
        ];
    }
            
    /**
     * Pengganti Http::get('.../products')
     */
    function getProduct()
    {
        // Mengambil data dari tabel products yang sudah kamu isi lewat Tinker
        $products = DB::table('products')->get()->map(function($item) {
            return [
                'product_id' => $item->id, // Penting: Menyelamatkan baris 39 & 43
                'name' => $item->name,
                'price' => $item->price
            ];
        })->toArray();

        if (empty($products)) {
            return [];
        }

        shuffle($products);
        
        // Ambil jumlah produk secara acak (minimal 1)
        return array_slice($products, 0, $this->faker->numberBetween(1, count($products)));
    }
}