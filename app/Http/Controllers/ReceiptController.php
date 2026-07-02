<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function generate($id)
    {
        $transaction = Transaction::with(['details.product', 'payments'])->findOrFail($id);

        $storeName = config('app.name', 'ERP POS');
        $storeAddress = 'Jl. Contoh No. 123, Kota';

        $data = [
            'store_name' => $storeName,
            'store_address' => $storeAddress,
            'transaction' => $transaction,
            'code' => 'TRX-' . now()->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
            'items' => $transaction->details->map(fn ($detail) => [
                'name' => $detail->product?->name ?? 'Produk #' . $detail->product_id,
                'sku' => $detail->product?->sku ?? '',
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'amount' => $detail->amount,
            ]),
            'payment' => $transaction->payments->first(),
        ];

        $pdf = Pdf::loadView('pdf.receipt', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('struk-' . $transaction->id . '.pdf');
    }
}
