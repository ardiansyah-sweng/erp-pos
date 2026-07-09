<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryService();
    }

    public function test_create_category(): void
    {
        $result = $this->service->create([
            'name' => 'Makanan',
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Makanan', $result->name);
        $this->assertTrue($result->fresh()->is_active);
    }

    public function test_create_category_with_parent(): void
    {
        $parent = Category::create(['name' => 'Induk']);

        $child = $this->service->create([
            'name' => 'Anak',
            'parent_id' => $parent->id,
        ]);

        $this->assertEquals($parent->id, $child->parent_id);
    }

    public function test_get_all_returns_only_parent_categories(): void
    {
        Category::create(['name' => 'Parent 1']);
        Category::create(['name' => 'Parent 2']);
        $parent = Category::first();
        Category::create(['name' => 'Child', 'parent_id' => $parent->id]);

        $all = $this->service->getAll();

        $this->assertCount(2, $all);
        $this->assertEquals('Parent 1', $all->first()->name);
    }

    public function test_get_all_eager_loads_children(): void
    {
        $parent = Category::create(['name' => 'Parent']);
        Category::create(['name' => 'Child 1', 'parent_id' => $parent->id]);
        Category::create(['name' => 'Child 2', 'parent_id' => $parent->id]);

        $all = $this->service->getAll();

        $this->assertCount(2, $all->first()->children);
    }

    public function test_update_category_berhasil(): void
    {
        $category = Category::create(['name' => 'Lama']);

        $result = $this->service->update($category->id, ['name' => 'Baru']);

        $this->assertNotNull($result);
        $this->assertEquals('Baru', $result->name);
    }

    public function test_update_category_jika_data_tidak_ditemukan(): void
    {
        $result = $this->service->update(999999, ['name' => 'Baru']);

        $this->assertNull($result);
    }

    public function test_delete_category_berhasil(): void
    {
        $category = Category::create(['name' => 'Hapus']);

        $result = $this->service->delete($category->id);

        $this->assertTrue($result);
        $this->assertNull(Category::find($category->id));
    }

    public function test_delete_category_jika_data_tidak_ditemukan(): void
    {
        $result = $this->service->delete(999999);

        $this->assertFalse($result);
    }
}
