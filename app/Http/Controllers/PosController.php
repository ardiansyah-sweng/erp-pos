<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $totalTransactions = Transaction::count();

        $totalRevenue = Transaction::sum('total');

        $totalItemsSold = TransactionDetail::sum('quantity');

        $topProduct = TransactionDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->first();

        return view('pos.index', compact(
            'totalTransactions',
            'totalRevenue',
            'totalItemsSold',
            'topProduct'
        ));
    }
}