<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
{
    $cashiers = collect([
        (object)['name' => 'Andi Nugroho', 'username' => 'andi.nugroho', 'created_at' => '2025-01-01'],
        (object)['name' => 'Siti Rahayu', 'username' => 'siti.rahayu', 'created_at' => '2025-03-15'],
    ]);

    return view('cashier.index', compact('cashiers'));
}
}