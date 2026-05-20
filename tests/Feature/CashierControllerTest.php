<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CashierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_index_route_returns_view_with_cashiers(): void
    {
        DB::table('cashiers')->insert([
            [
                'name'       => 'Rika Sari',
                'username'   => 'rika.sari',
                'password'   => bcrypt('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Andi Putra',
                'username'   => 'andi.putra',
                'password'   => bcrypt('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->get('/cashiers');

        $response->assertStatus(200);
        $response->assertViewIs('cashier.index');
        $response->assertViewHas('cashiers');
        $response->assertSee('Daftar Kasir');
        $response->assertSee('Rika Sari');
        $response->assertSee('andi.putra');
    }
}