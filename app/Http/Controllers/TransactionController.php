<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function getTransaction()
    {
        $purchaseOrders = Transaction::join('transaction_detail as td', 'transaction.id', '=', 'td.transaction_id')
        ->select('transaction.*', 'td.*')
        ->get();

        return response()->json($purchaseOrders);
    }

    /**
     * Tampilkan list semua transaksi
     * Route: /transactions
     */
    public function index()
    {
        $transactions = Transaction::with('details')
            ->latest()
            ->paginate(20);

        return view('transactions.index', ['transactions' => $transactions]);
    }

    /**
     * Tampilkan detail transaksi
     * Route: /transactions/{id}
     */
    public function show($id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        return view('transactions.show', ['transaction' => $transaction]);
    }

    /**
     * Cetak struk ke PDF
     * Route: /transactions/{id}/print-pdf
     */
    public function printPDF($id)
    {
        // Ambil data transaksi
        $transaction = Transaction::with('details')->findOrFail($id);

        // Generate PDF dari view
        $pdf = Pdf::loadView('struk', ['transaction' => $transaction])
            ->setPaper('a4', 'portrait');

        // Unduh file PDF
        return $pdf->download('struk_' . $transaction->id . '.pdf');
    }

    /**
     * Tampilkan preview struk
     * Route: /transactions/{id}/struk-preview
     */
    public function stukPreview($id)
    {
        $transaction = Transaction::with('details')->findOrFail($id);
        return view('struk', ['transaction' => $transaction]);
    }
}
