<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /** @test */
    public function halaman_login_dapat_diakses()
    {
        // Halaman login hanya dirender untuk tamu; kasir yang sudah login diarahkan ke dashboard.
        $this->flushSession();

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }
}