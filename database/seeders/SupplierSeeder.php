<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data awal supplier.
     * Membuat 10 data supplier dummy menggunakan SupplierFactory.
     */
    public function run(): void
    {
        // Buat 10 supplier aktif menggunakan factory
        Supplier::factory()->count(10)->create();
    }
}
