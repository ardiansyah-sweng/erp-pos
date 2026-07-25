<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_stock_adjustment_creates_history(): void
    {
        $product = $this->createProduct(10);

        $response = $this->putJson(route('stock-adjustments.update', $product), [
            'movement_type' => 'in',
            'quantity' => 5,
            'notes' => 'Barang dari gudang',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.old_stock', 10)
            ->assertJsonPath('data.new_stock', 15)
            ->assertJsonPath('data.movement.quantity', 5);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'in',
            'quantity' => 5,
            'stock_before' => 10,
            'stock_after' => 15,
            'notes' => 'Barang dari gudang',
        ]);
    }

    public function test_failed_stock_adjustment_does_not_create_history(): void
    {
        $product = $this->createProduct(2);

        $response = $this->putJson(route('stock-adjustments.update', $product), [
            'movement_type' => 'out',
            'quantity' => 3,
            'notes' => 'Melebihi stok',
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 2,
        ]);
    }

    public function test_stock_adjustment_page_displays_movement_history(): void
    {
        $product = $this->createProduct(10);

        StockMovement::create([
            'product_id' => $product->id,
            'movement_type' => 'adjustment',
            'quantity' => -2,
            'stock_before' => 10,
            'stock_after' => 8,
            'notes' => 'Selisih stok fisik',
        ]);

        $response = $this->get(route('stock-adjustments.index'));

        $response->assertOk()
            ->assertViewHas('stockMovements')
            ->assertSee('Riwayat Perubahan Stok')
            ->assertSee('Produk Riwayat')
            ->assertSee('Koreksi')
            ->assertSee('Selisih stok fisik');
    }

    private function createProduct(int $stockQuantity): Product
    {
        return Product::create([
            'sku' => 'HISTORY-001',
            'name' => 'Produk Riwayat',
            'selling_price' => 10000,
            'stock_quantity' => $stockQuantity,
            'min_stock' => 2,
            'is_active' => true,
        ]);
    }
}
