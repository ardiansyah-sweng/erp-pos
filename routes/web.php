<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
// 1. Tambahkan import controller baru kamu di sini
use App\Http\Controllers\CashierSearchController; 

// 2. Ubah rute '/' agar tidak ke 'welcome' lagi, tapi ke Controller Search
Route::get('/', [CashierSearchController::class, 'search']);

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);

// 3. (Opsional) Kamu juga bisa buat rute spesifik untuk search
Route::get('/cashier/search', [CashierSearchController::class, 'search'])->name('cashier.search');