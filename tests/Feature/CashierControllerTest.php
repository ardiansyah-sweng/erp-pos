<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\View;
use Mockery;

class CashierControllerTest extends TestCase
{
    /**
     * Test if the index method works properly.
     */
    public function test_cashier_index_returns_successful_response(): void
    {
        // 1. Mock CashierService karena belum dibuat oleh Aditya
        $mockService = Mockery::mock('App\Services\CashierService');
        $mockService->shouldReceive('getAllCashier')->once()->andReturn(['Data Kasir 1', 'Data Kasir 2']);

        // 2. Mock View Factory karena view 'cashier.index' belum dibuat oleh Farhan
        // Ini akan mencegah Laravel mencari file blade yang belum ada.
        View::shouldReceive('make')
            ->once()
            ->withArgs([
                'cashier.index',
                ['cashiers' => ['Data Kasir 1', 'Data Kasir 2']],
                [], // default mergeData dari helper view()
            ])
            ->andReturn(Mockery::mock(\Illuminate\View\View::class));

        // 3. Panggil Controller secara langsung
        $controller = new CashierController($mockService);
        $controller->index();

        // 4. Jika tidak ada error sampai di sini, berarti method index() Fathir sudah benar!
        $this->assertTrue(true);
    }
}
