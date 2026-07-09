<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan dan camilan', 'sort_order' => 1],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan panas', 'sort_order' => 2],
            ['name' => 'Perlengkapan', 'description' => 'Kebutuhan rumah tangga', 'sort_order' => 3],
            ['name' => 'Lainnya', 'description' => 'Produk lainnya', 'sort_order' => 4],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['name' => $data['name']], $data);
        }

        $makanan = Category::where('name', 'Makanan')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        if ($makanan) {
            Category::updateOrCreate(
                ['name' => 'Makanan Ringan'],
                ['description' => 'Camilan dan snack', 'parent_id' => $makanan->id, 'sort_order' => 1]
            );
            Category::updateOrCreate(
                ['name' => 'Makanan Berat'],
                ['description' => 'Makanan utama', 'parent_id' => $makanan->id, 'sort_order' => 2]
            );
        }

        if ($minuman) {
            Category::updateOrCreate(
                ['name' => 'Minuman Dingin'],
                ['description' => 'Minuman segar dan es', 'parent_id' => $minuman->id, 'sort_order' => 1]
            );
            Category::updateOrCreate(
                ['name' => 'Minuman Panas'],
                ['description' => 'Kopi, teh, dan minuman panas', 'parent_id' => $minuman->id, 'sort_order' => 2]
            );
        }
    }
}
