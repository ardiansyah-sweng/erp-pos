<?php

namespace Database\Seeders;

use App\Models\Cashiers;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    public function run(): void
    {
        Cashiers::insert([
            ['name' => 'Andi Nugroho', 'username' => 'andi.nugroho', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Siti Rahayu', 'username' => 'siti.rahayu', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Budi Santoso', 'username' => 'budi.santoso', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dewi Lestari', 'username' => 'dewi.lestari', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Raka Pratama', 'username' => 'raka.pratama', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nurul Hidayah', 'username' => 'nurul.hidayah', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fajar Ramadan', 'username' => 'fajar.ramadan', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ayu Wulandari', 'username' => 'ayu.wulandari', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rizky Firmansyah', 'username' => 'rizky.firmansyah', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mega Putri', 'username' => 'mega.putri', 'password' => bcrypt('password123'), 'created_at' => now(), 'updated_at' => now()],
        ]);
           
    }
}