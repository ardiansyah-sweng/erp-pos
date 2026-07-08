<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManageTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_page_displays_products(): void
    {
        Product::create([
            'sku' => 'PRD-001',
            'name' => 'Produk Sample',
            'selling_price' => 15000,
            'stock_quantity' => 10,
            'min_stock' => 2,
        ]);

        $response = $this->get(route('products.manage'));

        $response->assertOk();
        $response->assertViewIs('products.manage');
        $response->assertSee('Produk Sample');
    }

    public function test_store_creates_product(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Produk Baru',
            'sku' => 'PRD-002',
            'barcode' => '8998765432109',
            'selling_price' => 25000,
            'unit' => 'pcs',
            'stock_quantity' => 50,
            'min_stock' => 5,
            'description' => 'Test product',
        ]);

        $response->assertRedirect(route('products.manage'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'sku' => 'PRD-002',
            'name' => 'Produk Baru',
            'selling_price' => 25000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'sku', 'selling_price', 'unit', 'stock_quantity', 'min_stock']);
    }

    public function test_update_changes_product(): void
    {
        $product = Product::create([
            'sku' => 'PRD-003',
            'name' => 'Produk Lama',
            'selling_price' => 10000,
            'stock_quantity' => 20,
            'min_stock' => 3,
        ]);

        $response = $this->put(route('products.update', $product), [
            'name' => 'Produk Update',
            'sku' => 'PRD-003',
            'selling_price' => 20000,
            'unit' => 'pcs',
            'stock_quantity' => 25,
            'min_stock' => 5,
        ]);

        $response->assertRedirect(route('products.manage'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Update',
            'selling_price' => 20000,
        ]);
    }

    public function test_destroy_toggles_active(): void
    {
        $product = Product::create([
            'sku' => 'PRD-004',
            'name' => 'Produk Aktif',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'min_stock' => 2,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => true,
        ]);

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.manage'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }
}
