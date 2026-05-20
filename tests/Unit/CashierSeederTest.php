<?php

namespace Tests\Unit;

use Database\Seeders\CashierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashierSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_cashiers(): void
    {
        $this->seed(CashierSeeder::class);

        $this->assertDatabaseCount('cashiers', 2);
    }

    public function test_seeder_creates_correct_usernames(): void
    {
        $this->seed(CashierSeeder::class);

        $this->assertDatabaseHas('cashiers', ['username' => 'rika.sari']);
        $this->assertDatabaseHas('cashiers', ['username' => 'andi.putra']);
    }

    public function test_seeder_creates_correct_names(): void
    {
        $this->seed(CashierSeeder::class);

        $this->assertDatabaseHas('cashiers', ['name' => 'Rika Sari']);
        $this->assertDatabaseHas('cashiers', ['name' => 'Andi Putra']);
    }

    public function test_seeder_hashes_password(): void
    {
        $this->seed(CashierSeeder::class);

        $cashier = DB::table('cashiers')->where('username', 'rika.sari')->first();

        $this->assertTrue(Hash::check('password123', $cashier->password));
    }

    public function test_seeder_does_not_duplicate_on_rerun(): void
    {
        $this->seed(CashierSeeder::class);
        $this->seed(CashierSeeder::class);

        $this->assertDatabaseCount('cashiers', 2);
    }
}