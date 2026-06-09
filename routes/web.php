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

Route::get('/dashboard', function () {
    return view('erp-dashboard');
})->name('dashboard');

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('/sales-notes', [TransactionController::class, 'salesNotes'])->name('sales-notes');
Route::get('/sales-notes/pdf', [TransactionController::class, 'downloadSalesReportPdf'])->name('sales-notes.pdf');
