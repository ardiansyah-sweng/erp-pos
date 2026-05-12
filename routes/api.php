<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/transactions', [TransactionController::class, 'getTransaction']);
Route::post('/transactions', [TransactionController::class, 'storeTransaction']);