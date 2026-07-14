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
                'name'       => 'Admin',
                'username'   => 'admin@erp.test',
                'password'   => Hash::make('admin123'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
        $this->command->info('   Email: admin@erp.test    | Password: admin123');
        $this->command->info('   Email: kasir1@erp.test   | Password: kasir123');
    }
}