<?php

namespace Tests\Unit;

use App\Models\Cashiers;
use App\Services\HrisService;
use App\Services\LoginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.hris.url' => 'http://hris.test/api']);
    }

    public function test_username_tidak_ditemukan()
    {
        $service = new LoginService(new HrisService());

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

        $service = new LoginService(new HrisService());

        $result = $service->loginService('eka', 'passwordsalah');

        $this->assertFalse($result['success']);
        $this->assertEquals('Password salah.', $result['message']);
    }

    public function test_login_berhasil_jika_terdaftar_kasir_di_hris()
    {
        Http::fake([
            'hris.test/api/cashiers' => Http::response([
                'status' => 'success',
                'data' => [
                    ['email' => 'eka', 'status' => 'active'],
                ],
            ]),
        ]);

        $cashier = new Cashiers();
        $cashier->name = 'Eka';
        $cashier->username = 'eka';
        $cashier->password = Hash::make('password123');
        $cashier->save();

        $service = new LoginService(new HrisService());

        $result = $service->loginService('eka', 'password123');

        $this->assertTrue($result['success']);
        $this->assertEquals('Login berhasil.', $result['message']);
        $this->assertEquals($cashier->id, $result['data']->id);
    }

    public function test_login_ditolak_jika_bukan_kasir_terdaftar_di_hris()
    {
        Http::fake([
            'hris.test/api/cashiers' => Http::response([
                'status' => 'success',
                'data' => [],
            ]),
        ]);

        $cashier = new Cashiers();
        $cashier->name = 'Eka';
        $cashier->username = 'eka';
        $cashier->password = Hash::make('password123');
        $cashier->save();

        $service = new LoginService(new HrisService());

        $result = $service->loginService('eka', 'password123');

        $this->assertFalse($result['success']);
        $this->assertEquals('Akun tidak terdaftar sebagai kasir aktif di HRIS.', $result['message']);
    }
}
