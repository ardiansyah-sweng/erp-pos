<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_is_reduced_after_checkout()
    {
        $product = Product::create([
            'sku' => 'PRD-001',
            'barcode' => '8999999999999',
            'name' => 'Produk Test',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 20,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->postJson('/pos/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 10000,
                ]
            ],
            'payment_method' => 'cash',
            'cash_tendered' => 50000,
        ]);

        $response->assertStatus(201);

        $product->refresh();

        $this->assertEquals(18, $product->stock_quantity);
    }

    public function test_stock_is_reduced_for_multiple_items()
    {
        $product = Product::create([
            'sku' => 'PRD-002',
            'barcode' => '8888888888888',
            'name' => 'Produk Test 2',
            'unit' => 'pcs',
            'selling_price' => 5000,
            'stock_quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->postJson('/pos/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 10000,
                ]
            ],
            'payment_method' => 'cash',
            'cash_tendered' => 50000,
        ]);

        $product->refresh();

        $this->assertEquals(48, $product->stock_quantity);
    }
}