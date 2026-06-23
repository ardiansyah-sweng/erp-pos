<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CashierController;

Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/cashiers', [CashierController::class, 'index'])->name('cashiers.index');
Route::get('/cashiers/create', [CashierController::class, 'create'])->name('cashiers.create');
Route::post('/cashiers', [CashierController::class, 'store'])->name('cashiers.store');
Route::get('/cashiers/search', [CashierController::class, 'search'])->name('cashiers.search');
Route::put('/cashiers/{id}/reset-password', [CashierController::class, 'resetPassword'])->name('cashiers.reset-password');
Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);
