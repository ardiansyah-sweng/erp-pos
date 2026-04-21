<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);

// Routes untuk transaksi management
Route::get('/transactions-list', [TransactionController::class, 'index'])->name('transaction.index');
Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transaction.show');
Route::get('/transactions/{id}/struk-preview', [TransactionController::class, 'stukPreview'])->name('transaction.struk-preview');
Route::get('/transactions/{id}/print-pdf', [TransactionController::class, 'printPDF'])->name('transaction.print-pdf');
