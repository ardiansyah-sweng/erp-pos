<?php



use App\Services\JobroleService;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('welcome');
    
});
Route::get('/test-update', function () {
    $service = new JobroleService();

    $data = [
        'name' => 'Cashier Baru'
    ];

    return $service->updateCashier(1, $data);
});

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);