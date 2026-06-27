<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return redirect()->route('pos.index');
});

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');

Route::get('/products', [ProductController::class, 'getProducts'])->name('products.index');
Route::get('/products/sku/{sku}', [ProductController::class, 'getItemBySKU']);

Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/pos/checkout', [TransactionController::class, 'checkout']);

Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);


Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
Route::post('/cashier/add', [CashierController::class, 'add'])->name('cashier.add');
Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');

Route::get('/customers', [CustomerController::class,'getCustomers']);
Route::post('/customers', [CustomerController::class,'store']);
Route::get('/customers/search', [CustomerController::class,'search'])
    ->name('customers.search');
Route::get('/customers/{id}', [CustomerController::class,'show']);
Route::put('/customers/{id}', [CustomerController::class,'update']);
Route::delete('/customers/{id}', [CustomerController::class,'destroy']);

Route::get('/members', [CustomerController::class, 'index'])->name('members.index');
Route::post('/members/store', [CustomerController::class, 'store'])->name('members.store');

