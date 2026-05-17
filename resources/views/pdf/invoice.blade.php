<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembelian </title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            margin: 2px 0;
            font-size: 12px;
        }

        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            font-size: 12px;
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        .total {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

<div class="header">
    <p class="title">INVOICE PEMBELIAN</p>
    <p class="subtitle">Invoice #{{ $transaction_number }}</p>
    <p class="subtitle">{{ $transaction->created_at }}</p>
</div>

<hr>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th class="right">Qty</th>
            <th class="right">Price</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaction->details as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->price) }}</td>
            <td class="right">{{ number_format($item->amount) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<hr>

<div class="total">
    TOTAL: Rp {{ number_format($transaction->total) }}
</div>

<div class="footer">
    Terima kasih telah berbelanja
</div>

</body>
</html>