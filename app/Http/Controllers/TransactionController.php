<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function getTransaction()
    {
        $purchaseOrders = Transaction::join('transaction_detail as td', 'transaction.id', '=', 'td.transaction_id')
        ->select('transaction.*', 'td.*')
        ->get();

        return response()->json($purchaseOrders);
    }

    public function storeTransaction(Request $request)
    {
        $service = new TransactionService();

        $transactionId = $service->storeTransaction($request->items);

        return response()->json([
            'message' => 'Transaction Success',
            'transaction_id' => $transactionId
        ]);
    }
}