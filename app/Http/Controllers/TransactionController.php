<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function getTransaction()
    {
        $transactions = Transaction::with('details')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,e_wallet,bank_transfer'],
            'cash_tendered' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        $subtotal = 0;
        $detailRows = [];

        foreach ($validated['items'] as $item) {
            $product = $products->get($item['product_id']);
            $quantity = (int) $item['quantity'];
            $unitPrice = (int) $item['unit_price'];
            $lineTotal = $quantity * $unitPrice;

            $subtotal += $lineTotal;

            $detailRows[] = [
                'product' => [
                    'id' => $product?->id,
                    'name' => $product?->name,
                    'sku' => $product?->sku,
                ],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
            ];
        }

        $discountAmount = (int) ($validated['discount_amount'] ?? 0);
        $totalAmount = max(0, $subtotal - $discountAmount);
        $cashTendered = (int) ($validated['cash_tendered'] ?? 0);
        $changeAmount = $validated['payment_method'] === 'cash' ? max(0, $cashTendered - $totalAmount) : 0;
        $paymentStatus = $validated['payment_method'] === 'cash' && $cashTendered < $totalAmount ? 'pending' : 'paid';

        $transaction = DB::transaction(function () use ($validated, $totalAmount) {
            $transaction = Transaction::create([
                'total' => $totalAmount,
            ]);

            foreach ($validated['items'] as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => (string) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (int) $item['unit_price'],
                    'amount' => (int) $item['quantity'] * (int) $item['unit_price'],
                ]);
            }

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => [
                'id' => $transaction->id,
                'transaction_number' => 'TRX-' . now()->format('YmdHis') . '-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'cash_tendered' => $cashTendered,
                'change_amount' => $changeAmount,
                'details' => $detailRows,
            ],
        ], 201);
    }

    public function salesNotes()
    {
        return view('transactions.sales-notes', [
            'transactions' => $this->getSalesTransactions(),
        ]);
    }

    public function downloadSalesReportPdf()
    {
        $transactions = $this->getSalesTransactions();
        $pdf = $this->buildSalesReportPdf($transactions);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-penjualan.pdf"',
        ]);
    }

    private function getSalesTransactions()
    {
        return Transaction::query()
            ->leftJoin('transaction_detail as td', 'transaction.id', '=', 'td.transaction_id')
            ->select([
                'transaction.id',
                'transaction.total',
                'transaction.created_at',
                'td.product_id',
                'td.quantity',
                'td.price',
                'td.amount',
            ])
            ->orderByDesc('transaction.created_at')
            ->orderByDesc('transaction.id')
            ->get()
            ->groupBy('id')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'id' => $first->id,
                    'transaction_code' => 'TRX-' . str_pad($first->id, 6, '0', STR_PAD_LEFT),
                    'date' => $first->created_at,
                    'total' => $first->total,
                    'item_count' => $items->whereNotNull('product_id')->sum('quantity'),
                    'details' => $items->whereNotNull('product_id')->values(),
                ];
            })
            ->values();
    }

    private function buildSalesReportPdf($transactions)
    {
        $lines = [
            'LAPORAN PENJUALAN',
            'Tanggal export: ' . now()->format('Y-m-d H:i'),
            '',
            'Jumlah Transaksi: ' . $transactions->count(),
            'Total Penjualan: Rp' . number_format($transactions->sum('total'), 0, ',', '.'),
            'Total Item Terjual: ' . $transactions->sum('item_count'),
            '',
        ];

        if ($transactions->isEmpty()) {
            $lines[] = 'Belum ada data penjualan.';
        }

        foreach ($transactions as $transaction) {
            $lines[] = $transaction['transaction_code'] . ' | ' . ($transaction['date'] ? $transaction['date']->format('Y-m-d H:i') : 'Tanggal belum tersedia');
            $lines[] = 'Total Transaksi: Rp' . number_format($transaction['total'], 0, ',', '.') . ' | Item: ' . $transaction['item_count'];

            if ($transaction['details']->isEmpty()) {
                $lines[] = '- Belum ada detail barang untuk transaksi ini.';
            }

            foreach ($transaction['details'] as $detail) {
                $lines[] = '- Produk ' . $detail->product_id
                    . ' | Qty ' . $detail->quantity
                    . ' | Harga Rp' . number_format($detail->price, 0, ',', '.')
                    . ' | Subtotal Rp' . number_format($detail->amount, 0, ',', '.');
            }

            $lines[] = '';
        }

        return $this->makeSimplePdf($lines);
    }

    private function makeSimplePdf(array $lines)
    {
        $pages = array_chunk($lines, 42);
        $objects = [];
        $pageIds = [];
        $fontId = 3 + (count($pages) * 2);

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        foreach ($pages as $index => $pageLines) {
            $pageId = 3 + ($index * 2);
            $contentId = $pageId + 1;
            $pageIds[] = $pageId;

            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . $fontId . ' 0 R >> >> /Contents ' . $contentId . ' 0 R >>';

            $stream = "BT\n/F1 10 Tf\n50 800 Td\n";
            foreach ($pageLines as $lineIndex => $line) {
                if ($lineIndex > 0) {
                    $stream .= "0 -17 Td\n";
                }
                $stream .= '(' . $this->escapePdfText(substr($line, 0, 100)) . ") Tj\n";
            }
            $stream .= "ET\n";

            $objects[$contentId] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', array_map(fn ($id) => $id . ' 0 R', $pageIds)) . '] /Count ' . count($pageIds) . ' >>';
        $objects[$fontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= str_pad($offsets[$id], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText($text)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    public function store(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric',
            'details' => 'required|array'
        ]);

        $transaction = Transaction::create([
            'total' => $request->total
        ]);

        foreach ($request->details as $detail) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
                'amount' => $detail['quantity'] * $detail['price']
            ]);
        }

        return response()->json([
            'message' => 'Transaction berhasil ditambahkan',
            'data' => $transaction
        ], 201);
    }
}
