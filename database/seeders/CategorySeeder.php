<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Minuman' => 'Air mineral, teh, kopi, dan susu kemasan',
            'Makanan Instan' => 'Mi instan dan makanan siap saji',
            'Camilan' => 'Snack dan biskuit',
            'Kebutuhan Rumah' => 'Perlengkapan dan pembersih rumah tangga',
        ];

        foreach ($categories as $name => $description) {
            Category::updateOrCreate(
                ['name' => $name],
                ['description' => $description, 'is_active' => true]
            );
        }

        // Tautkan produk contoh (dari ProductSeeder) ke kategori berdasarkan SKU.
        $productCategory = [
            'PRD-001' => 'Minuman',        // Aqua 600ml
            'PRD-002' => 'Makanan Instan', // Indomie Goreng
            'PRD-003' => 'Minuman',        // Teh Kotak 300ml
            'PRD-004' => 'Camilan',        // Snack Kentang
            'PRD-005' => 'Minuman',        // Susu UHT 250ml
            'PRD-006' => 'Camilan',        // Biskuit Cokelat
            'PRD-007' => 'Minuman',        // Kopi Sachet
            'PRD-008' => 'Kebutuhan Rumah',// Sabun Cuci Piring
        ];

        $categoryIds = Category::pluck('id', 'name');

        foreach ($productCategory as $sku => $categoryName) {
            Product::where('sku', $sku)->update([
                'category_id' => $categoryIds[$categoryName] ?? null,
            ]);
        }
    }
}
