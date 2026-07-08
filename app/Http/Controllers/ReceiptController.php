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

        $payment = $transaction->payments->first();
        $referenceNumber = $payment?->reference_number ?? '';

        // Parse KEMASAN dari reference_number (format: "KEMASAN:500|Kantong Sedang")
        $packagingFee = 0;
        $packagingName = '';
        if (preg_match('/KEMASAN:(\d+)\|([^;]+)/i', $referenceNumber, $m)) {
            $packagingFee = (int) $m[1];
            $packagingName = trim($m[2]);
        }

        // Parse PARKIR dari reference_number (format: "PARKIR:2000|Motor")
        $parkingFee = 0;
        $parkingName = '';
        if (preg_match('/PARKIR:(\d+)\|([^;]+)/i', $referenceNumber, $m)) {
            $parkingFee = (int) $m[1];
            $parkingName = trim($m[2]);
        }

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
            'payment' => $payment,
            'parking_fee' => $parkingFee,
            'parking_name' => $parkingName,
            'packaging_fee' => $packagingFee,
            'packaging_name' => $packagingName,
        ];

        $pdf = Pdf::loadView('pdf.receipt', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('struk-' . $transaction->id . '.pdf');
    }
}
