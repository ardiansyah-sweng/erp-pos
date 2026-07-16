<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureCashierAuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_login_saat_akses_halaman_terproteksi(): void
    {
        $this->flushSession();

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_halaman_login_tetap_bisa_diakses_tanpa_login(): void
    {
        $this->flushSession();

        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_kasir_yang_sudah_login_bisa_akses_halaman_terproteksi(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }
}
