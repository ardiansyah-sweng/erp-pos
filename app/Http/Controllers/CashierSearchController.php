<?php

namespace App\Http\Controllers;

use App\Services\CashierService; 
use Illuminate\Http\Request;

class CashierSearchController extends Controller
{
    protected $cashierService;

    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }
    public function search(Request $request)
{
    $keyword = $request->query('q');
    $results = $this->cashierService->findCashier($keyword);

    // Kirim data ke file view index
    return view('cashier.index', ['cashiers' => $results]);
}
}