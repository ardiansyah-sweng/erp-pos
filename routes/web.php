<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCrudController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');

// ← baris Route::get('/products', ...) yang lama DIHAPUS
Route::resource('products', ProductCrudController::class); // ← ganti jadi ini

Route::get('/prices', [ProductController::class, 'getPrices']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);
Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'getApiProducts']);
});