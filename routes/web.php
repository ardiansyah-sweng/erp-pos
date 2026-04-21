<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jalankan-schedule', [ScheduleController::class, 'index'])->name('jalankan-schedule-index');
Route::post('/jalankan-schedule', [ScheduleController::class, 'run'])->name('jalankan-schedule');
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);