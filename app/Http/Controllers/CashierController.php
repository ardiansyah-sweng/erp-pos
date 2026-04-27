<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CashierService;

class CashierController extends Controller
{
    protected $cashierService;

    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }

    public function index()
    {
        // Memanggil method getAllCashier() dari CashierService (Tugas Aditya Wardana)
        $cashiers = $this->cashierService->getAllCashier();

        // Menampilkan view cashier.index (Tugas Farhan Muhammad Iqbal)
        return view('cashier.index', compact('cashiers'));
    }
}
