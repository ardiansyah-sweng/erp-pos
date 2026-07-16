<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\JobroleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class JobroleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_cashier_berhasil()
    {
        DB::table('cashiers')->insert([
            'id' => 999,
            'name' => 'Cashier Lama',
            'username' => 'cashierlama',
            'password' => '123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = new JobroleService();

        $result = $service->updateCashier(999, [
            'name' => 'Cashier Baru',
        ]);

        $this->assertNotNull($result);
        $this->assertEquals('Cashier Baru', $result->name);

        DB::table('cashiers')->where('id', 999)->delete();
    }

    public function test_update_cashier_jika_data_tidak_ditemukan()
    {
        $service = new JobroleService();

        $result = $service->updateCashier(999999, [
            'name' => 'Cashier Baru',
        ]);

        $this->assertNull($result);
    }
}