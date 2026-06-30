<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\TaskController;
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

Route::get('/tasks', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::post('/tasks', [TaskController::class, 'store'])
    ->name('tasks.store');

Route::put('/tasks/{task}', [TaskController::class, 'update'])
    ->name('tasks.update');

Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
    ->name('tasks.destroy');

Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);
Route::get('/transactions/{id}/receipt', [ReceiptController::class, 'generate'])->name('transactions.receipt');



Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
