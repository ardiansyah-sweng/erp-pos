<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CashierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tabel cashiers ada
     */
    public function test_cashiers_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('cashiers'));
    }

    /**
     * Test kolom tabel cashiers
     */
    public function test_cashiers_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('cashiers', [
            'id',
            'name',
            'email',
            'phone',
            'created_at',
            'updated_at',
        ]));
    }
}