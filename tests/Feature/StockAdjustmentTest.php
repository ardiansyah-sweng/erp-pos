<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_page_has_link_to_stock_adjustment_page(): void
    {
        $response = $this->get(route('pos.index'));

        $response->assertOk();
        $response->assertSee(route('stock-adjustments.index'), false);
        $response->assertSee('Penyesuaian Stok');
    }

    public function test_stock_adjustment_page_displays_active_products(): void
    {
        Product::create([
            'sku' => 'STOCK-001',
            'name' => 'Produk Penyesuaian',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('stock-adjustments.index'));

        $response->assertOk();
        $response->assertViewIs('stock-adjustments.index');
        $response->assertSee('Produk Penyesuaian');
    }

    public function test_stock_in_increases_product_stock(): void
    {
        $product = Product::create([
            'sku' => 'STOCK-002',
            'name' => 'Produk Stok Masuk',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->putJson(route('stock-adjustments.update', $product), [
            'movement_type' => 'in',
            'quantity' => 5,
            'notes' => 'Barang datang',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.old_stock', 10)
            ->assertJsonPath('data.new_stock', 15)
            ->assertJsonPath('data.movement.quantity', 5);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15,
        ]);
    }

    public function test_signed_adjustment_can_reduce_product_stock(): void
    {
        $product = Product::create([
            'sku' => 'STOCK-003',
            'name' => 'Produk Koreksi',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->putJson(route('stock-adjustments.update', $product), [
            'movement_type' => 'adjustment',
            'quantity' => -3,
            'notes' => 'Selisih stok fisik',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.old_stock', 10)
            ->assertJsonPath('data.new_stock', 7)
            ->assertJsonPath('data.movement.quantity', -3);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7,
        ]);
    }

    public function test_stock_cannot_be_reduced_below_zero(): void
    {
        $product = Product::create([
            'sku' => 'STOCK-004',
            'name' => 'Produk Stok Terbatas',
            'selling_price' => 10000,
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $response = $this->putJson(route('stock-adjustments.update', $product), [
            'movement_type' => 'out',
            'quantity' => 5,
            'notes' => 'Barang rusak',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 4,
        ]);
    }
}
