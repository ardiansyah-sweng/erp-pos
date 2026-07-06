<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 6px; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .number { text-align: right; }
        .summary { margin-bottom: 14px; }
        .summary div { margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <div class="muted">Dicetak dari sistem ERP POS</div>

    <div class="summary">
        <div><strong>Jumlah Transaksi:</strong> {{ $transactions->count() }}</div>
        <div><strong>Total Penjualan:</strong> Rp{{ number_format($transactions->sum('total'), 0, ',', '.') }}</div>
        <div><strong>Total Item Terjual:</strong> {{ $transactions->sum('item_count') }}</div>
        @if ($startDate || $endDate)
            <div><strong>Rentang Tanggal:</strong> {{ $startDate ?? 'awal' }} sampai {{ $endDate ?? 'akhir' }}</div>
        @endif
    </div>

    @foreach ($transactions as $transaction)
        <div style="margin-bottom: 12px;">
            <strong>{{ $transaction['transaction_code'] }}</strong><br>
            <span class="muted">{{ $transaction['date'] ? $transaction['date']->translatedFormat('d F Y H:i') : 'Tanggal belum tersedia' }} · {{ $transaction['item_count'] }} item</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="number">Qty</th>
                    <th class="number">Harga</th>
                    <th class="number">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction['details'] as $detail)
                    <tr>
                        <td>{{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}</td>
                        <td class="number">{{ $detail->quantity }}</td>
                        <td class="number">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td class="number">Rp{{ number_format($detail->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
