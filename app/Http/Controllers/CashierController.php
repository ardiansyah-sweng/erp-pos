<?php

namespace App\Http\Controllers;

use App\Services\CashierService;
use App\Models\Cashier;
use Illuminate\Http\Request;
class CashierController extends Controller
{
    protected $cashierService;

    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }

    public function index()
    {
        $cashiers = $this->cashierService->getAllCashier();
        return view('cashier.index', compact('cashiers'));
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'email'    => 'required|email',
    ]);

    Cashier::findOrFail($id)->update($validated);

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Cashier updated successfully']);
    }

    return redirect()->route('cashier.index');
}
}