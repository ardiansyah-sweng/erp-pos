<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CashierSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}