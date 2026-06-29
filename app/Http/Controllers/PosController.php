<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $todayStats = DB::table('transaction as t')
            ->leftJoin('transaction_detail as td', 't.id', '=', 'td.transaction_id')
            ->whereDate('t.created_at', today())
            ->selectRaw('COALESCE(SUM(t.total), 0) as total_revenue, COALESCE(SUM(td.quantity), 0) as total_products, COUNT(DISTINCT t.id) as total_transactions')
            ->first();

        return view('pos.index', compact('todayStats'));
    }
}