<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_endpoint_returns_success_response(): void
    {
        $response = $this->getJson('/products');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJson(['success' => true]);
    }

    public function test_search_by_name(): void
    {
        Product::create([
            'sku' => 'SKU-001',
            'name' => 'Aqua Botol',
            'selling_price' => 5000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->getJson('/products?search=Aqua');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_search_by_sku(): void
    {
        Product::create([
            'sku' => 'SKU-XYZ',
            'name' => 'Produk Test',
            'selling_price' => 3000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $response = $this->getJson('/products?search=SKU-XYZ');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_search_by_barcode(): void
    {
        Product::create([
            'sku' => 'SKU-002',
            'barcode' => '8991234567890',
            'name' => 'Produk Barcode',
            'selling_price' => 7000,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $response = $this->getJson('/products?search=8991234567890');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_inactive_products_are_excluded(): void
    {
        Product::create([
            'sku' => 'SKU-003',
            'name' => 'Produk Nonaktif',
            'selling_price' => 1000,
            'stock_quantity' => 5,
            'is_active' => false,
        ]);

        $response = $this->getJson('/products?search=Nonaktif');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(0, $response->json('data'));
    }
}
