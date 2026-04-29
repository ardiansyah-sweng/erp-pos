<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan getItemBySKU mengembalikan produk yang benar.
     */
    public function test_get_item_by_sku_returns_correct_product(): void
    {
        Product::create([
            'sku'           => 'PROD001',
            'name'          => 'Sample Product',
            'selling_price' => 25000,
        ]);

        $product = Product::getItemBySKU('PROD001');

        $this->assertNotNull($product);
        $this->assertEquals('PROD001', $product->sku);
        $this->assertEquals('Sample Product', $product->name);
    }

    /**
     * Memastikan getItemBySKU mengembalikan null jika SKU tidak ditemukan.
     */
    public function test_get_item_by_sku_returns_null_when_not_found(): void
    {
        $product = Product::getItemBySKU('TIDAK_ADA');

        $this->assertNull($product);
    }
}