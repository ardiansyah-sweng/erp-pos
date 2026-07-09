<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('categories'));
    }

    public function test_categories_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('categories', [
            'id',
            'name',
            'description',
            'parent_id',
            'sort_order',
            'is_active',
            'created_at',
            'updated_at',
        ]));
    }
}
