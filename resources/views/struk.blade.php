<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 10px;
            font-size: 12px;
            width: 80mm;
        }
        .container {
            max-width: 100%;
            text-align: center;
        }
        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .transaction-info {
            text-align: left;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            font-size: 11px;
        }
        .transaction-info p {
            margin: 3px 0;
        }
        .items {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            text-align: left;
        }
        .item-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 2fr;
            gap: 5px;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .item-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 2fr;
            gap: 5px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .item-name {
            white-space: normal;
            word-break: break-word;
        }
        .qty {
            text-align: center;
        }
        .price {
            text-align: right;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .summary {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        .summary-row.total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }
        .footer p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead {
            border-bottom: 1px solid #000;
        }
        table th {
            text-align: left;
            padding: 3px 0;
            font-size: 10px;
            font-weight: bold;
        }
        table td {
            padding: 3px 0;
            font-size: 11px;
            border-bottom: 1px dotted #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>STRUK PENJUALAN</h2>
            <p>================================</p>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <p><strong>No. Transaksi:</strong> {{ $transaction->id }}</p>
            <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="40%">Produk</th>
                    <th width="15%">Qty</th>
                    <th width="20%">Harga</th>
                    <th width="25%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                <tr>
                    <td>{{ $detail->product_id }}</td>
                    <td style="text-align: center;">{{ $detail->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td style="text-align: right;"><strong>Rp {{ number_format($detail->amount, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Jumlah Barang:</span>
                <span>{{ $transaction->details->sum('quantity') }} item(s)</span>
            </div>
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah berbelanja</p>
            <p>{{ now()->format('d/m/Y H:i:s') }}</p>
            <p>================================</p>
        </div>
    </div>
</body>
</html>
