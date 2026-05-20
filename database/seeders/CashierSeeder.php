<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Cashiers;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashiers = [
            [
                'name' => 'Rika Sari',
                'username' => 'rika.sari',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Andi Putra',
                'username' => 'andi.putra',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($cashiers as $cashier) {
            Cashiers::updateOrCreate(
                ['username' => $cashier['username']],
                $cashier
            );
        }
    }
}
