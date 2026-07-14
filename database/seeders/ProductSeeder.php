<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'name');

        $products = [
            ['barcode' => '8991002100010', 'sku' => 'PRD-001', 'name' => 'Aqua 600ml', 'description' => 'Air mineral botol', 'unit' => 'bottle', 'selling_price' => 4000, 'stock_quantity' => 120, 'min_stock' => 20, 'category' => 'Minuman'],
            ['barcode' => '8992002100011', 'sku' => 'PRD-002', 'name' => 'Indomie Goreng', 'description' => 'Mi instan goreng', 'unit' => 'pcs', 'selling_price' => 3500, 'stock_quantity' => 200, 'min_stock' => 40, 'category' => 'Makanan'],
            ['barcode' => '8993002100012', 'sku' => 'PRD-003', 'name' => 'Teh Kotak 300ml', 'description' => 'Minuman teh manis', 'unit' => 'box', 'selling_price' => 5000, 'stock_quantity' => 90, 'min_stock' => 15, 'category' => 'Minuman'],
            ['barcode' => '8994002100013', 'sku' => 'PRD-004', 'name' => 'Snack Kentang', 'description' => 'Camilan ringan', 'unit' => 'pcs', 'selling_price' => 8000, 'stock_quantity' => 75, 'min_stock' => 10, 'category' => 'Makanan Ringan'],
            ['barcode' => '8995002100014', 'sku' => 'PRD-005', 'name' => 'Susu UHT 250ml', 'description' => 'Susu kemasan', 'unit' => 'pcs', 'selling_price' => 6000, 'stock_quantity' => 60, 'min_stock' => 10, 'category' => 'Minuman'],
            ['barcode' => '8996002100015', 'sku' => 'PRD-006', 'name' => 'Biskuit Cokelat', 'description' => 'Biskuit rasa cokelat', 'unit' => 'pack', 'selling_price' => 12000, 'stock_quantity' => 50, 'min_stock' => 8, 'category' => 'Makanan Ringan'],
            ['barcode' => '8997002100016', 'sku' => 'PRD-007', 'name' => 'Kopi Sachet', 'description' => 'Kopi instan sachet', 'unit' => 'box', 'selling_price' => 10000, 'stock_quantity' => 45, 'min_stock' => 10, 'category' => 'Minuman Panas'],
            ['barcode' => '8998002100017', 'sku' => 'PRD-008', 'name' => 'Sabun Cuci Piring', 'description' => 'Pembersih dapur', 'unit' => 'bottle', 'selling_price' => 15000, 'stock_quantity' => 32, 'min_stock' => 5, 'category' => 'Perlengkapan'],
        ];

        foreach ($products as $product) {
            $categoryName = $product['category'];
            unset($product['category']);

            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product + [
                    'is_active' => true,
                    'category_id' => $categoryIds[$categoryName] ?? null,
                ]
            );
        }
    }
}
