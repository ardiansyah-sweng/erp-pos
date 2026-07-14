<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('cashiers')->count() > 0) {
            $this->command->info('Cashier sudah ada, skip seeder.');
            return;
        }

        DB::table('cashiers')->insert([
            [
                'name'       => 'Kasir 1',
                'username'   => 'kasir1@erp.test',
                'password'   => Hash::make('kasir123'),
                'role'       => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Data kasir berhasil dibuat!');
        $this->command->info('   Email: kasir1@erp.test   | Password: kasir123');
        $this->command->info('   Catatan: email ini juga harus terdaftar sebagai kasir aktif di HRIS agar bisa login.');
    }
}