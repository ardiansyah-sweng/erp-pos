<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'product_id'     => 'PRD001',
                'barcode'        => '899100000001',
                'name'           => 'Beras Premium 5kg',
                'description'    => 'Beras premium untuk kebutuhan harian.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'pack',
                'cost_price'     => 68000,
                'selling_price'  => 72000,
                'stock_quantity' => 40,
                'min_stock'      => 5,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD002',
                'barcode'        => '899100000002',
                'name'           => 'Minyak Goreng 1L',
                'description'    => 'Minyak goreng kemasan 1 liter.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'botol',
                'cost_price'     => 17000,
                'selling_price'  => 19500,
                'stock_quantity' => 75,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD003',
                'barcode'        => '899100000003',
                'name'           => 'Gula Pasir 1kg',
                'description'    => 'Gula pasir kristal putih.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'pack',
                'cost_price'     => 14500,
                'selling_price'  => 16000,
                'stock_quantity' => 60,
                'min_stock'      => 8,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD004',
                'barcode'        => '899100000004',
                'name'           => 'Mi Instan Goreng',
                'description'    => 'Mi instan rasa goreng.',
                'category_id'    => 2,
                'category_name'  => 'Makanan Cepat Saji',
                'unit'           => 'pcs',
                'cost_price'     => 2800,
                'selling_price'  => 3500,
                'stock_quantity' => 200,
                'min_stock'      => 25,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD005',
                'barcode'        => '899100000005',
                'name'           => 'Air Mineral 600ml',
                'description'    => 'Air mineral botol 600ml.',
                'category_id'    => 3,
                'category_name'  => 'Minuman',
                'unit'           => 'botol',
                'cost_price'     => 2500,
                'selling_price'  => 4000,
                'stock_quantity' => 120,
                'min_stock'      => 20,
                'is_active'      => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
