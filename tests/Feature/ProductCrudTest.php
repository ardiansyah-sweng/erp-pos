<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_product_list()
    {
        Product::create([
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk Test',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->get('/product-list');

        $response->assertStatus(200);
        $response->assertSee('Produk Test');
    }

    public function test_can_create_product()
    {
        $response = $this->post('/product-list', [
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk Test',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU001',
            'name' => 'Produk Test',
        ]);
    }

    public function test_can_update_product()
    {
        $product = Product::create([
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk Lama',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->put('/product-list/' . $product->id, [
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk Baru',
            'unit' => 'pcs',
            'selling_price' => 15000,
            'stock_quantity' => 25,
            'min_stock' => 3,
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Baru',
            'stock_quantity' => 25,
            'selling_price' => 15000,
        ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::create([
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk Hapus',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 50,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->delete('/product-list/' . $product->id);

        $response->assertRedirect();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}