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
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Kasir 1',
                'username'   => 'kasir1',
                'password'   => Hash::make('kasir123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Data kasir berhasil dibuat!');
        $this->command->info('   Username: admin    | Password: admin123');
        $this->command->info('   Username: kasir1   | Password: kasir123');
    }
}