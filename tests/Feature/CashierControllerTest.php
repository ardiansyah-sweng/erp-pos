<?php

namespace Tests\Feature;

use App\Models\Cashiers;
use App\Http\Controllers\CashierController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CashierControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Daftarkan route secara dinamis untuk keperluan testing saja
        // Tanpa mengubah routes/web.php
        Route::get('/cashiers-test', [CashierController::class, 'index']);
    }

    /**
     * Test the cashier index page returns a successful response.
     */
    public function test_cashier_index_returns_successful_response(): void
    {
        $response = $this->get('/cashiers-test');

        $response->assertStatus(200);
    }

    /**
     * Test the cashier index page returns the correct view.
     */
    public function test_cashier_index_returns_correct_view(): void
    {
        $response = $this->get('/cashiers-test');

        $response->assertStatus(200);
        $response->assertViewIs('cashier.index');
    }

    /**
     * Test the cashier index page passes cashiers data to the view.
     */
    public function test_cashier_index_passes_cashiers_data_to_view(): void
    {
        // Buat data secara manual tanpa menggunakan Factory
        Cashiers::forceCreate([
            'name' => 'Test Cashier',
            'username' => 'testcashier',
            'password' => bcrypt('password')
        ]);

        $response = $this->get('/cashiers-test');

        $response->assertStatus(200);
        $response->assertViewIs('cashier.index');
        $response->assertViewHas('cashiers');
        $this->assertCount(1, $response->viewData('cashiers'));
    }

    /**
     * Test the cashier index page works with no cashiers in database.
     */
    public function test_cashier_index_works_with_empty_database(): void
    {
        $response = $this->get('/cashiers-test');

        $response->assertStatus(200);
        $response->assertViewIs('cashier.index');
        $response->assertViewHas('cashiers');
        $this->assertCount(0, $response->viewData('cashiers'));
    }
}