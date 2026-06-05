<?php

namespace Tests\Unit;

use App\Models\Cashiers;
use App\Services\LoginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_username_tidak_ditemukan()
    {
        $service = new LoginService();

        $result = $service->loginService('eka', '123456');

        $this->assertFalse($result['success']);
        $this->assertEquals('Username tidak ditemukan.', $result['message']);
    }

    public function test_password_salah()
    {
        $cashier = new Cashiers();
        $cashier->name = 'Eka';
        $cashier->username = 'eka';
        $cashier->password = Hash::make('passwordbenar');
        $cashier->save();

        $service = new LoginService();

        $result = $service->loginService('eka', 'passwordsalah');

        $this->assertFalse($result['success']);
        $this->assertEquals('Password salah.', $result['message']);
    }

    public function test_login_berhasil()
    {
        $cashier = new Cashiers();
        $cashier->name = 'Eka';
        $cashier->username = 'eka';
        $cashier->password = Hash::make('password123');
        $cashier->save();

        $service = new LoginService();

        $result = $service->loginService('eka', 'password123');

        $this->assertTrue($result['success']);
        $this->assertEquals('Login berhasil.', $result['message']);
        $this->assertEquals($cashier->id, $result['data']->id);
    }
}