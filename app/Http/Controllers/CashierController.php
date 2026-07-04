<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use App\Services\JobroleService;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = Cashiers::all();

        return view('cashier.index', compact('cashiers'));
    }

    public function update(Request $request, $id, JobroleService $jobroleService)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        $cashier = $jobroleService->updateCashier($id, $validated);

        if (!$cashier) {
            abort(404);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cashier updated successfully']);
        }

        return redirect()->back();
    }
}