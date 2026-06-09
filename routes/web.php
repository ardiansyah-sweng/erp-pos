<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCrudController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LoginController;

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');

Route::resource('products', ProductCrudController::class);

Route::get('/prices', [ProductController::class, 'getPrices']);
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');

Route::get('/', function () {
    return view('pos.index');
});

Route::get('/pos', function () {
    return view('pos.index');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'getApiProducts']);
});
Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/transaction/store', [TransactionController::class, 'store']);
