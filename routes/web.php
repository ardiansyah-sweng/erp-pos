<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCrudController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');

Route::resource('products', ProductCrudController::class);

Route::get('/prices', [ProductController::class, 'getPrices']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);

Route::get('/', function () {
    return view('pos.index');
});

Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'getApiProducts']);
    
});

