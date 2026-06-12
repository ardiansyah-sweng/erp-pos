<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');

Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/product-list', [ProductController::class, 'index'])->name('products.list');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::get('/transaction-list',[TransactionController::class, 'index'])->name('transactions.list');

Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::get('/transactions/{id}/pdf', [TransactionController::class, 'downloadPdf']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/transaction/store', [TransactionController::class, 'store']);

