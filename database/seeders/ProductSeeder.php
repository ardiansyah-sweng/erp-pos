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
                'sku'            => 'PRD001',
                'barcode'        => '8991002100010',
                'name'           => 'Aqua 600ml',
                'description'    => 'Air mineral botol',
                'unit'           => 'bottle',
                'selling_price'  => 4000,
                'stock_quantity' => 120,
                'min_stock'      => 20,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD002',
                'barcode'        => '8992002100011',
                'name'           => 'Indomie Goreng',
                'description'    => 'Mi instan goreng',
                'unit'           => 'pcs',
                'selling_price'  => 3500,
                'stock_quantity' => 200,
                'min_stock'      => 40,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD003',
                'barcode'        => '8993002100012',
                'name'           => 'Teh Kotak 300ml',
                'description'    => 'Minuman teh manis',
                'unit'           => 'box',
                'selling_price'  => 5000,
                'stock_quantity' => 90,
                'min_stock'      => 15,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD004',
                'barcode'        => '8994002100013',
                'name'           => 'Snack Kentang',
                'description'    => 'Camilan ringan',
                'unit'           => 'pcs',
                'selling_price'  => 8000,
                'stock_quantity' => 75,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD005',
                'barcode'        => '8995002100014',
                'name'           => 'Susu UHT 250ml',
                'description'    => 'Susu kemasan',
                'unit'           => 'pcs',
                'selling_price'  => 6000,
                'stock_quantity' => 60,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD006',
                'barcode'        => '8996002100015',
                'name'           => 'Biskuit Cokelat',
                'description'    => 'Biskuit rasa cokelat',
                'unit'           => 'pack',
                'selling_price'  => 12000,
                'stock_quantity' => 50,
                'min_stock'      => 8,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD007',
                'barcode'        => '8997002100016',
                'name'           => 'Kopi Sachet',
                'description'    => 'Kopi instan sachet',
                'unit'           => 'box',
                'selling_price'  => 10000,
                'stock_quantity' => 45,
                'min_stock'      => 10,
                'is_active'      => true,
            ],
            [
                'sku'            => 'PRD008',
                'barcode'        => '8998002100017',
                'name'           => 'Sabun Cuci Piring',
                'description'    => 'Pembersih dapur',
                'unit'           => 'bottle',
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
