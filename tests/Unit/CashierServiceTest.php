<?php

namespace Tests\Unit;

use App\Models\Cashiers;
use App\Services\CashierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CashierServiceTest extends TestCase
{
    use RefreshDatabase;

    private CashierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CashierService();
    }

    public function test_get_all_cashier_returns_all_cashiers(): void
    {
        DB::table('cashiers')->insert([
            [
                'name'       => 'Budi Santoso',
                'username'   => 'budi.santoso',
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

        $result = $this->service->getAllCashier();

        $this->assertCount(2, $result);
    }

    public function test_get_all_cashier_returns_ordered_by_name(): void
    {
        DB::table('cashiers')->insert([
            [
                'name'       => 'Zara Aulia',
                'username'   => 'zara.aulia',
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
            [
                'name'       => 'Mira Sari',
                'username'   => 'mira.sari',
                'password'   => bcrypt('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $result = $this->service->getAllCashier();

        $this->assertEquals('Andi Putra', $result[0]->name);
        $this->assertEquals('Mira Sari',  $result[1]->name);
        $this->assertEquals('Zara Aulia', $result[2]->name);
    }

    public function test_get_all_cashier_returns_empty_when_no_cashiers(): void
    {
        $result = $this->service->getAllCashier();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}