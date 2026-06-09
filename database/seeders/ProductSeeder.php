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
                'barcode'        => '8991002100010',
                'name'           => 'Aqua 600ml',
                'description'    => 'Air mineral botol',
                'category_id'    => 1,
                'category_name'  => 'Minuman',
                'unit'           => 'bottle',
                'cost_price'     => 3000,
                'selling_price'  => 4000,
                'stock_quantity' => 120,
                'min_stock'      => 20,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD002',
                'barcode'        => '8992002100011',
                'name'           => 'Indomie Goreng',
                'description'    => 'Mi instan goreng',
                'category_id'    => 2,
                'category_name'  => 'Makanan',
                'unit'           => 'pcs',
                'cost_price'     => 2800,
                'selling_price'  => 3500,
                'stock_quantity' => 200,
                'min_stock'      => 40,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD003',
                'barcode'        => '8993002100012',
                'name'           => 'Teh Kotak 300ml',
                'description'    => 'Minuman teh manis',
                'category_id'    => 1,
                'category_name'  => 'Minuman',
                'unit'           => 'box',
                'cost_price'     => 3500,
                'selling_price'  => 5000,
                'stock_quantity' => 90,
                'min_stock'      => 15,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD004',
                'barcode'        => '8994002100013',
                'name'           => 'Snack Kentang',
                'description'    => 'Camilan ringan',
                'category_id'    => 2,
                'category_name'  => 'Makanan',
                'unit'           => 'pcs',
                'cost_price'     => 6000,
                'selling_price'  => 8000,
                'stock_quantity' => 75,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD005',
                'barcode'        => '8995002100014',
                'name'           => 'Susu UHT 250ml',
                'description'    => 'Susu kemasan',
                'category_id'    => 1,
                'category_name'  => 'Minuman',
                'unit'           => 'pcs',
                'cost_price'     => 4500,
                'selling_price'  => 6000,
                'stock_quantity' => 60,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD006',
                'barcode'        => '8996002100015',
                'name'           => 'Biskuit Cokelat',
                'description'    => 'Biskuit rasa cokelat',
                'category_id'    => 2,
                'category_name'  => 'Makanan',
                'unit'           => 'pack',
                'cost_price'     => 9000,
                'selling_price'  => 12000,
                'stock_quantity' => 50,
                'min_stock'      => 8,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD007',
                'barcode'        => '8997002100016',
                'name'           => 'Kopi Sachet',
                'description'    => 'Kopi instan sachet',
                'category_id'    => 1,
                'category_name'  => 'Minuman',
                'unit'           => 'box',
                'cost_price'     => 7500,
                'selling_price'  => 10000,
                'stock_quantity' => 45,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'product_id'     => 'PRD008',
                'barcode'        => '8998002100017',
                'name'           => 'Sabun Cuci Piring',
                'description'    => 'Pembersih dapur',
                'category_id'    => 3,
                'category_name'  => 'Kebersihan',
                'unit'           => 'bottle',
                'cost_price'     => 11000,
                'selling_price'  => 15000,
                'stock_quantity' => 32,
                'min_stock'      => 5,
                'is_active'      => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}