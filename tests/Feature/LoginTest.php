<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_berhasil_login()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password123'
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard'); // sesuaikan
    }

    /** @test */
    public function login_gagal_password_salah()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'salah'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function login_gagal_email_tidak_terdaftar()
    {
        $response = $this->post('/login', [
            'email' => 'tidak@ada.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function login_gagal_input_kosong()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }
}