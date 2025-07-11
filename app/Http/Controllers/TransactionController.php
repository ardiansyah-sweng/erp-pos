<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;

class TransactionController extends Controller
{
    public function getTransaction()
    {
        $purchaseOrders = Transaction::join('transaction_detail as td', 'transaction.id', '=', 'td.transaction_id')
        ->select('transaction.*', 'td.*')
        ->get();

        return response()->json($purchaseOrders);
    }
}
