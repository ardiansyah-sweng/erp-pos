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

        // Parse reference_number for extras (PARKIR, KEMASAN, etc.)
        $parkingFee = 0;
        $parkingName = '';
        $packagingFee = 0;
        $packagingName = '';

        if ($payment && $payment->reference_number) {
            foreach (explode(';', $payment->reference_number) as $segment) {
                $segment = trim($segment);
                if (str_starts_with($segment, 'PARKIR:')) {
                    $parts = explode('|', substr($segment, 7), 2);
                    $parkingFee = (int) ($parts[0] ?? 0);
                    $parkingName = $parts[1] ?? '';
                } elseif (str_starts_with($segment, 'KEMASAN:')) {
                    $parts = explode('|', substr($segment, 8), 2);
                    $packagingFee = (int) ($parts[0] ?? 0);
                    $packagingName = $parts[1] ?? '';
                }
            }
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
