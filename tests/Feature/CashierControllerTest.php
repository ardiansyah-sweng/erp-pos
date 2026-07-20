<?php

namespace Tests\Feature;

use App\Http\Controllers\CashierController;
use App\Models\Cashiers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
            'password' => bcrypt('password'),
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

    public function test_check_username_reports_availability(): void
    {
        Cashiers::create([
            'name' => 'Kasir Lama',
            'username' => 'kasirlama',
            'password' => 'password123',
        ]);

        $this->getJson('/cashier/check-username?username=kasirlama')
            ->assertOk()
            ->assertJsonPath('available', false);

        $this->getJson('/cashier/check-username?username=kasirbaru')
            ->assertOk()
            ->assertJsonPath('available', true);
    }

    public function test_add_creates_cashier_with_hashed_password(): void
    {
        $response = $this->post('/cashier/add', [
            'name' => 'Kasir Baru',
            'username' => 'kasirbaru',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cashier.index'));

        $cashier = Cashiers::where('username', 'kasirbaru')->firstOrFail();
        $this->assertSame('Kasir Baru', $cashier->name);
        $this->assertTrue(Hash::check('password123', $cashier->password));
    }

    public function test_edit_returns_cashier_data(): void
    {
        $cashier = Cashiers::create([
            'name' => 'Kasir Satu',
            'username' => 'kasirsatu',
            'password' => 'password123',
        ]);

        $this->getJson("/cashier/{$cashier->id}/edit")
            ->assertOk()
            ->assertJsonPath('data.username', 'kasirsatu')
            ->assertJsonMissingPath('data.password');
    }

    public function test_update_changes_cashier_without_replacing_empty_password(): void
    {
        $cashier = Cashiers::create([
            'name' => 'Nama Lama',
            'username' => 'namalama',
            'password' => 'password123',
        ]);
        $oldPassword = $cashier->password;

        $response = $this->put("/cashier/{$cashier->id}", [
            'name' => 'Nama Baru',
            'username' => 'namabaru',
            'password' => '',
        ]);

        $response->assertRedirect(route('cashier.index'));
        $cashier->refresh();

        $this->assertSame('Nama Baru', $cashier->name);
        $this->assertSame('namabaru', $cashier->username);
        $this->assertSame($oldPassword, $cashier->password);
    }
}
