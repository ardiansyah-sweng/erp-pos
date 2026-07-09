<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManageTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_categories(): void
    {
        Category::create(['name' => 'Minuman']);

        $response = $this->get(route('categories.index'));

        $response->assertOk();
        $response->assertViewIs('categories.index');
        $response->assertSee('Minuman');
    }

    public function test_store_creates_category(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'Makanan',
            'description' => 'Produk makanan',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'name' => 'Makanan',
            'is_active' => true,
        ]);
    }

    public function test_store_validates_required_and_unique_name(): void
    {
        Category::create(['name' => 'Snack']);

        $this->post(route('categories.store'), [])
            ->assertSessionHasErrors('name');

        $this->post(route('categories.store'), ['name' => 'Snack'])
            ->assertSessionHasErrors('name');
    }

    public function test_update_changes_category(): void
    {
        $category = Category::create(['name' => 'Lama']);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Baru',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Baru',
        ]);
    }

    public function test_destroy_toggles_active(): void
    {
        $category = Category::create(['name' => 'Aktif']);

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_product_can_be_assigned_to_category(): void
    {
        $category = Category::create(['name' => 'Elektronik']);

        $this->post(route('products.store'), [
            'name' => 'Kabel USB',
            'category_id' => $category->id,
            'sku' => 'ELK-001',
            'selling_price' => 15000,
            'unit' => 'pcs',
            'stock_quantity' => 30,
            'min_stock' => 5,
        ])->assertRedirect(route('products.manage'));

        $this->assertDatabaseHas('products', [
            'sku' => 'ELK-001',
            'category_id' => $category->id,
        ]);

        $product = Product::where('sku', 'ELK-001')->first();
        $this->assertSame('Elektronik', $product->category->name);
    }

    public function test_delete_permanently_removes_empty_category(): void
    {
        $category = Category::create(['name' => 'Kosong']);

        $this->delete(route('categories.delete', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_is_blocked_when_category_has_products(): void
    {
        $category = Category::create(['name' => 'Berisi']);
        Product::create([
            'sku' => 'CAT-001',
            'name' => 'Produk Berkategori',
            'category_id' => $category->id,
            'selling_price' => 10000,
            'stock_quantity' => 5,
            'min_stock' => 1,
        ]);

        $this->delete(route('categories.delete', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_product_store_rejects_invalid_category(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Produk Tanpa Kategori Valid',
            'category_id' => 999,
            'sku' => 'INV-001',
            'selling_price' => 10000,
            'unit' => 'pcs',
            'stock_quantity' => 10,
            'min_stock' => 2,
        ])->assertSessionHasErrors('category_id');
    }
}
