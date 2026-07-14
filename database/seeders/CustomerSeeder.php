<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $customers = [
            ['name' => 'Budi Santoso',    'phone' => '081234567890', 'points' => 3200],
            ['name' => 'Siti Rahmawati',  'phone' => '081234567891', 'points' => 1800],
            ['name' => 'Ahmad Hidayat',   'phone' => '081234567892', 'points' => 750],
            ['name' => 'Dewi Lestari',    'phone' => '081234567893', 'points' => 150],
            ['name' => 'Rudi Hermawan',   'phone' => '081234567894', 'points' => 2800],
            ['name' => 'Ani Wijaya',      'phone' => '081234567895', 'points' => 5000],
            ['name' => 'Hendra Gunawan',  'phone' => '081234567896', 'points' => 1200],
            ['name' => 'Rina Marlina',    'phone' => '081234567897', 'points' => 0],
            ['name' => 'Doni Prasetyo',   'phone' => '081234567898', 'points' => 4100],
            ['name' => 'Mega Sari',       'phone' => '081234567899', 'points' => 600],
            ['name' => 'Agus Wijaya',     'phone' => '081234567800', 'points' => 2200],
            ['name' => 'Fitri Handayani', 'phone' => '081234567801', 'points' => 350],
            ['name' => 'Bayu Saputra',    'phone' => '081234567802', 'points' => 1600],
            ['name' => 'Indah Permata',   'phone' => '081234567803', 'points' => 900],
            ['name' => 'Eko Prasetyo',    'phone' => '081234567804', 'points' => 10],
        ];

        $now = now();

        foreach ($customers as $i => $data) {
            $points = $data['points'];

            if ($points >= 3000) {
                $level = 'Platinum';
            } elseif ($points >= 1500) {
                $level = 'Gold';
            } elseif ($points >= 500) {
                $level = 'Silver';
            } else {
                $level = 'Regular';
            }

            Customer::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'customer_code' => 'CUS' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'name'          => $data['name'],
                    'email'         => $faker->optional(0.6)->email(),
                    'address'       => $faker->optional(0.7)->address(),
                    'points'        => $points,
                    'member_level'  => $level,
                    'created_at'    => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at'    => $now,
                ]
            );
        }
    }
}
