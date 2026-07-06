<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])
    ->name('jalankan-schedule-index');

Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])
    ->name('jalankan-schedule');

Route::get('/products', [ProductController::class, 'getProducts'])
    ->name('products.index');

Route::get('/products/sku/{sku}', [ProductController::class, 'getItemBySKU']);

Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])
    ->name('stock-adjustments.index');

Route::put('/stock-adjustments/{product}', [StockAdjustmentController::class, 'update'])
    ->name('stock-adjustments.update');

Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);
Route::get('/transactions/{id}/receipt', [ReceiptController::class, 'generate'])->name('transactions.receipt');
Route::get('/sales-notes', [TransactionController::class, 'salesNotes'])->name('sales-notes');
Route::get('/sales-notes/pdf', [TransactionController::class, 'downloadSalesReportPdf'])->name('sales-notes.pdf');
Route::get('/returns', [ReturnTransactionController::class, 'index'])->name('returns.index');
Route::post('/returns/{transaction}', [ReturnTransactionController::class, 'store'])->name('returns.store');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');


Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
