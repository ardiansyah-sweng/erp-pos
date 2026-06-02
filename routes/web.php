<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
// 1. Tambahkan import controller baru kamu di sini
use App\Http\Controllers\CashierSearchController; 
use App\Http\Controllers\LoginController;

// 2. Ubah rute '/' agar tidak ke 'welcome' lagi, tapi ke Controller Search
Route::get('/', [CashierSearchController::class, 'search']);
Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);

// 3. (Opsional) Kamu juga bisa buat rute spesifik untuk search
Route::get('/cashier/search', [CashierSearchController::class, 'search'])->name('cashier.search');
Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
