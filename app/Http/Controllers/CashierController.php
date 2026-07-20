<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use App\Services\CashierService;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    protected CashierService $cashierService;

    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }

    /**
     * Cek ketersediaan username (untuk real-time AJAX check)
     */
    public function checkUsername(Request $request)
    {
        $username  = trim((string) $request->get('username', ''));
        $excludeId = $request->integer('exclude_id'); // untuk mode edit

        if ($username === '') {
            return response()->json(['available' => null]);
        }

        $query = Cashiers::where('username', $username);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $taken = $query->exists();

        return response()->json([
            'available' => !$taken,
            'message'   => $taken ? 'Username sudah digunakan.' : 'Username tersedia.',
        ]);
    }

    /**
     * Tampilkan daftar semua kasir
     */
    public function index()
    {
        $cashiers = $this->cashierService->getAllCashier();

        return view('cashier.index', compact('cashiers'));
    }
}