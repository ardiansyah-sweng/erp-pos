<?php

namespace App\Http\Controllers;

use App\Services\CashierService;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(CashierService $cashierService)
    {
        $cashiers = $cashierService->getAllCashier();

        return view('cashier.index', compact('cashiers'));
    }
}