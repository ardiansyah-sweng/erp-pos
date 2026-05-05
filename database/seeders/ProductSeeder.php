<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    protected \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create('id_ID');
    }

    public function run(): void
    {
        $categories = ['Makanan', 'Minuman', 'Snack', 'Elektronik', 'Lainnya'];

        for ($i = 0; $i < 10; $i++) {
            $id = strtoupper($this->faker->bothify('??####')); // format seperti di transaction_detail

            Product::create([
                'id'          => $id,
                'name'        => $this->faker->words(3, true),
                'category'    => $this->faker->randomElement($categories),
                'price'       => $this->faker->numberBetween(5, 100) * 1000,
                'stock'       => $this->faker->numberBetween(0, 100),
                'description' => $this->faker->sentence(),
            ]);
        }
    }
}