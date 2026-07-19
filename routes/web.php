<?php

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SyncHistoryController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\DashboardController;

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

Route::get('/products/detail/{sku}', fn (string $sku) => view('products.show', ['sku' => $sku]))
    ->name('products.show');

Route::get('/products/manage', [ProductController::class, 'manage'])->name('products.manage');
Route::post('/products/manage', [ProductController::class, 'store'])->name('products.store');
Route::put('/products/manage/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/manage/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/stock-adjustments', [StockAdjustmentController::class, 'index'])
    ->name('stock-adjustments.index');

Route::put('/stock-adjustments/{product}', [StockAdjustmentController::class, 'update'])
    ->name('stock-adjustments.update');

Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export');
Route::get('/transactions', [TransactionController::class, 'getTransaction'])->name('transactions.index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/pos/checkout', [TransactionController::class, 'checkout']);

Route::get('/api/low-stock', [ProductController::class, 'getLowStock'])->name('api.low-stock');
Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');




Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
Route::get('/cashier/check-username', [CashierController::class, 'checkUsername'])->name('cashier.check-username');
Route::post('/cashier/add', [CashierController::class, 'add'])->name('cashier.add');
Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
Route::get('/cashier/{id}/edit', [CashierController::class, 'edit'])->name('cashier.edit');
Route::put('/cashier/{id}', [CashierController::class, 'update'])->name('cashier.update');

Route::get('/customers', [CustomerController::class,'getCustomers']);
Route::post('/customers', [CustomerController::class,'store']);
Route::get('/customers/search', [CustomerController::class,'search'])
    ->name('customers.search');
Route::get('/customers/{id}', [CustomerController::class,'show']);
Route::put('/customers/{id}', [CustomerController::class,'update']);
Route::delete('/customers/{id}', [CustomerController::class,'destroy']);

Route::get('/members', [CustomerController::class, 'index'])->name('members.index');
Route::post('/members/store', [CustomerController::class, 'store'])->name('members.store');
Route::post('/pos/checkout', [TransactionController::class, 'checkout'])->name('pos.checkout');
Route::post('/transaction/store', [TransactionController::class, 'store']);
Route::get('/transactions/{id}/receipt', [ReceiptController::class, 'generate'])->name('transactions.receipt');
Route::get('/sales-notes', [TransactionController::class, 'salesNotes'])->name('sales-notes');
Route::get('/sales-notes/pdf', [TransactionController::class, 'downloadSalesReportPdf'])->name('sales-notes.pdf');
Route::get('/returns', [ReturnTransactionController::class, 'index'])->name('returns.index');
Route::post('/returns/{transaction}', [ReturnTransactionController::class, 'store'])->name('returns.store');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/members', [ReportController::class, 'memberReport'])->name('reports.members');
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
Route::get('/discounts/active-for-products', [DiscountController::class, 'getActiveForProducts']);

Route::get('/sync-histories', [SyncHistoryController::class, 'index'])->name('sync-histories.index');















Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ===== Supplier Management =====
use App\Http\Controllers\SupplierController;

Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
