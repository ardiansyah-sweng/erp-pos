<?php

namespace Tests\Feature;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnamePrintTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_stock_opname_page_displays_active_products(): void
    {
        Carbon::setTestNow('2026-07-23 12:00:00');

        $activeProduct = $this->createProduct('OPNAME-001', 'Produk Aktif', true);
        $this->createProduct('OPNAME-002', 'Produk Nonaktif', false);

        $response = $this->get(route('stock-opname.print'));

        $response->assertOk()
            ->assertViewIs('stock-adjustments.print-opname')
            ->assertViewHas('products', fn ($products) => $products->contains($activeProduct))
            ->assertSee('Form Stok Opname')
            ->assertSee('Produk Aktif')
            ->assertDontSee('Produk Nonaktif')
            ->assertSee('Stok Sistem')
            ->assertSee('Stok Fisik')
            ->assertSee('Selisih')
            ->assertSee('23 July 2026');
    }

    public function test_stock_opname_page_does_not_modify_product_stock(): void
    {
        $product = $this->createProduct('OPNAME-003', 'Produk Tetap', true, 25);

        $this->get(route('stock-opname.print'))->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 25,
        ]);
    }

    public function test_stock_adjustment_page_contains_print_link(): void
    {
        $response = $this->get(route('stock-adjustments.index'));

        $response->assertOk()
            ->assertSee(route('stock-opname.print'), false)
            ->assertSee('Cetak Stok Opname');
    }

    private function createProduct(
        string $sku,
        string $name,
        bool $isActive,
        int $stockQuantity = 10
    ): Product {
        return Product::create([
            'sku' => $sku,
            'name' => $name,
            'selling_price' => 10000,
            'unit' => 'pcs',
            'stock_quantity' => $stockQuantity,
            'min_stock' => 2,
            'is_active' => $isActive,
        ]);
    }
}
