<?php

namespace Tests\Feature;

use App\Models\Cashiers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_cashier_page_can_be_opened(): void
    {
        $response = $this->get(route('cashiers.create'));

        $response->assertStatus(200);
        $response->assertViewIs('cashier.create');
    }

    public function test_store_cashier_creates_new_cashier(): void
    {
        $response = $this->post(route('cashiers.store'), [
            'name' => 'Kasir Baru',
            'username' => 'kasirbaru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('cashiers.index'));
        $response->assertSessionHas('success', 'Cashier berhasil ditambahkan.');

        $this->assertDatabaseHas('cashiers', [
            'name' => 'Kasir Baru',
            'username' => 'kasirbaru',
        ]);

        $cashier = Cashiers::where('username', 'kasirbaru')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $cashier->password));
    }

    public function test_search_cashier_returns_matching_cashiers_as_json(): void
    {
        Cashiers::create([
            'name' => 'Budi Cashier',
            'username' => 'budi',
            'password' => 'password123',
        ]);

        Cashiers::create([
            'name' => 'Siti Cashier',
            'username' => 'siti',
            'password' => 'password123',
        ]);

        $response = $this->getJson(route('cashiers.search', [
            'keyword' => 'budi',
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'name' => 'Budi Cashier',
                'username' => 'budi',
            ])
            ->assertJsonMissing([
                'username' => 'siti',
            ]);
    }

    public function test_reset_password_updates_cashier_password(): void
    {
        $cashier = Cashiers::create([
            'name' => 'Kasir Lama',
            'username' => 'kasirlama',
            'password' => 'password123',
        ]);

        $response = $this->put(route('cashiers.reset-password', $cashier->id), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('cashiers.index'));
        $response->assertSessionHas('success', 'Password cashier berhasil direset.');

        $cashier->refresh();
        $this->assertTrue(Hash::check('newpassword123', $cashier->password));
    }
}
