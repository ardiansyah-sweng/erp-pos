<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_index_returns_successful_response(): void
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
    }

    public function test_category_index_returns_correct_view(): void
    {
        $response = $this->get('/categories');

        $response->assertViewIs('category.index');
    }

    public function test_category_index_passes_categories_data_to_view(): void
    {
        Category::create(['name' => 'Makanan']);

        $response = $this->get('/categories');

        $response->assertViewHas('categories');
        $this->assertCount(1, $response->viewData('categories'));
    }

    public function test_category_index_works_with_empty_database(): void
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $this->assertCount(0, $response->viewData('categories'));
    }

    public function test_store_category_berhasil(): void
    {
        $response = $this->post('/categories', [
            'name' => 'Minuman',
            'sort_order' => 2,
        ]);

        $response->assertRedirect('/categories');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['name' => 'Minuman']);
    }

    public function test_store_category_with_parent(): void
    {
        $parent = Category::create(['name' => 'Induk']);

        $response = $this->post('/categories', [
            'name' => 'Anak',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Anak',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_category_validation_fails_without_name(): void
    {
        $response = $this->post('/categories', []);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_category_berhasil(): void
    {
        $category = Category::create(['name' => 'Lama']);

        $response = $this->put('/categories/' . $category->id, [
            'name' => 'Baru',
        ]);

        $response->assertRedirect('/categories');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Baru']);
    }

    public function test_update_category_not_found(): void
    {
        $response = $this->put('/categories/999999', [
            'name' => 'Baru',
        ]);

        $response->assertRedirect('/categories');
        $response->assertSessionHas('error');
    }

    public function test_destroy_category_berhasil(): void
    {
        $category = Category::create(['name' => 'Hapus']);

        $response = $this->delete('/categories/' . $category->id);

        $response->assertRedirect('/categories');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_category_not_found(): void
    {
        $response = $this->delete('/categories/999999');

        $response->assertRedirect('/categories');
        $response->assertSessionHas('error');
    }
}
