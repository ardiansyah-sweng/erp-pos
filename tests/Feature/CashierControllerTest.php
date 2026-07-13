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
        // Pakai middleware 'web' agar $errors tersedia di view
        Route::middleware('web')->get('/cashiers-test', [CashierController::class, 'index']);
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
     * Test tambah kasir sukses
     */
    public function test_add_cashier_successfully(): void
    {
        $response = $this->post('/cashier/add', [
            'name'                  => 'Kasir Baru',
            'username'              => 'kasirbaru',
            'password'              => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertRedirect(route('cashier.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cashiers', [
            'name'     => 'Kasir Baru',
            'username' => 'kasirbaru',
        ]);
    }

    /**
     * Test tambah kasir gagal — username sudah digunakan
     */
    public function test_add_cashier_fails_with_duplicate_username(): void
    {
        Cashiers::forceCreate([
            'name'     => 'Kasir Lama',
            'username' => 'kasirdup',
            'password' => bcrypt('Password1'),
        ]);

        $response = $this->post('/cashier/add', [
            'name'                  => 'Kasir Lain',
            'username'              => 'kasirdup',
            'password'              => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('username');
    }

    /**
     * Test tambah kasir gagal — password tidak memenuhi syarat
     */
    public function test_add_cashier_fails_with_weak_password(): void
    {
        $response = $this->post('/cashier/add', [
            'name'                  => 'Kasir Test',
            'username'              => 'kasirtest',
            'password'              => 'lemah',
            'password_confirmation' => 'lemah',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test halaman edit kasir tampil dengan data yang benar
     */
    public function test_edit_cashier_page_shows_correct_data(): void
    {
        $cashier = Cashiers::forceCreate([
            'name'     => 'Kasir Edit',
            'username' => 'kasiredit',
            'password' => bcrypt('Password1'),
        ]);

        $response = $this->get(route('cashier.edit', $cashier->id));

        $response->assertStatus(200);
        $response->assertViewIs('cashier.edit');
        $response->assertViewHas('cashier');
        $response->assertSee('Kasir Edit');
    }

    /**
     * Test update data kasir sukses
     */
    public function test_update_cashier_successfully(): void
    {
        $cashier = Cashiers::forceCreate([
            'name'     => 'Kasir Lama',
            'username' => 'kasirlama',
            'password' => bcrypt('Password1'),
        ]);

        $response = $this->put(route('cashier.update', $cashier->id), [
            'name'     => 'Kasir Diperbarui',
            'username' => 'kasirperbarui',
        ]);

        $response->assertRedirect(route('cashier.index'));
        $this->assertDatabaseHas('cashiers', [
            'id'       => $cashier->id,
            'name'     => 'Kasir Diperbarui',
            'username' => 'kasirperbarui',
        ]);
    }
}
