<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $cashiers = Cashiers::all();

        return view('cashier.index', compact('cashiers'));
    }

    public function destroy($id)
    {
        Cashiers::findOrFail($id)->delete();

        return redirect()->route('cashier.index');
    }
}