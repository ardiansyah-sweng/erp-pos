<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('erp-dashboard');
});

Route::get('/dashboard', function () {
    return view('erp-dashboard');
})->name('dashboard');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);

// FITUR LAPORAN PENJUALAN
Route::get('/sales-notes', [TransactionController::class, 'salesNotes'])->name('sales-notes');
Route::get('/sales-notes/pdf', [TransactionController::class, 'downloadSalesReportPdf'])->name('sales-notes.pdf');
