<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px 25px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #000;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 11px;
            color: #555;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .info-section div {
            width: 48%;
        }
        .info-section table {
            width: 100%;
            font-size: 11px;
        }
        .info-section td {
            padding: 1px 0;
        }
        .info-section .label {
            color: #666;
            width: 90px;
        }
        .info-section .value {
            font-weight: bold;
            color: #000;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 10px;
            padding: 6px 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 16px;
        }
        table.items th {
            background: #2c3e50;
            color: #fff;
            padding: 7px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.items th.right { text-align: right; }
        table.items td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        table.items td.right { text-align: right; }
        table.items tr:last-child td { border-bottom: none; }
        .summary {
            margin-left: auto;
            width: 280px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .summary td {
            padding: 4px 8px;
        }
        .summary .label { color: #555; }
        .summary .value { text-align: right; font-weight: bold; }
        .summary .total td {
            border-top: 2px solid #333;
            font-size: 14px;
            font-weight: bold;
            padding-top: 6px;
        }
        .payment-info {
            margin-top: 14px;
            padding: 10px 12px;
            background: #f5f6fa;
            border-radius: 4px;
            font-size: 11px;
        }
        .payment-info table { width: 100%; }
        .payment-info td { padding: 2px 0; }
        .payment-info .label { color: #555; width: 120px; }
        .payment-info .value { font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        .signature {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .signature div { text-align: center; width: 45%; }
        .signature .line {
            margin-top: 44px;
            border-top: 1px solid #333;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $store_name }}</h1>
        <p>{{ $store_address }}</p>
        <p>Telp: (021) 1234-5678 &nbsp;|&nbsp; Email: info{{ '@' }}erp-pos.com</p>
    </div>

    <div class="info-section">
        <div>
            <table>
                <tr><td class="label">No. Invoice</td><td class="value">: {{ $code }}</td></tr>
                <tr><td class="label">Tanggal</td><td class="value">: {{ $transaction->created_at?->translatedFormat('d F Y') }}</td></tr>
                <tr><td class="label">Jam</td><td class="value">: {{ $transaction->created_at?->format('H:i') }} WIB</td></tr>
            </table>
        </div>
        <div>
            <table>
                <tr><td class="label">Status</td><td class="value">: LUNAS</td></tr>
            </table>
        </div>
    </div>

    <div class="title">RINCIAN BELANJA</div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th style="width:70px;">SKU</th>
                <th>Nama Item</th>
                <th class="right" style="width:50px;">Qty</th>
                <th class="right" style="width:90px;">Harga</th>
                <th class="right" style="width:90px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['sku'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="right">{{ $item['quantity'] }}</td>
                <td class="right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($item['amount'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $parkingFeeAmount = (int) ($payment?->parking_fee ?? 0);
        $parkingFeeLabel  = ucfirst($payment?->parking_type ?? '');
        $subtotal = $transaction->total - $parkingFeeAmount + ($payment?->discount_amount ?? 0);
    @endphp

    <table class="summary">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($payment && $payment->discount_amount > 0)
        <tr>
            <td class="label">Diskon</td>
            <td class="value" style="color:#e74c3c;">- Rp {{ number_format($payment->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if ($parkingFeeAmount > 0)
        <tr>
            <td class="label">Parkir ({{ $parkingFeeLabel }})</td>
            <td class="value" style="color:#d97706;">+ Rp {{ number_format($parkingFeeAmount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total">
            <td class="label">Total Bayar</td>
            <td class="value">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($payment)
    <div class="payment-info">
        <table>
            <tr><td class="label">Metode Pembayaran</td><td class="value">: {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
            <tr><td class="label">Status</td><td class="value">: {{ strtoupper($payment->payment_status) }}</td></tr>
            @if ($payment->payment_method === 'cash')
            <tr><td class="label">Tunai</td><td class="value">: Rp {{ number_format($payment->cash_tendered, 0, ',', '.') }}</td></tr>
            <tr><td class="label">Kembalian</td><td class="value">: Rp {{ number_format($payment->change_amount, 0, ',', '.') }}</td></tr>
            @endif
            @if ($payment->reference_number)
            <tr><td class="label">No. Referensi</td><td class="value">: {{ $payment->reference_number }}</td></tr>
            @endif
        </table>
    </div>
    @endif

    <div class="signature">
        <div>
            <div>Hormat Kami,</div>
            <div class="line">( {{ $store_name }} )</div>
        </div>
        <div>
            <div>Pelanggan,</div>
            <div class="line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
        </div>
    </div>

    <div class="footer">
        Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan<br>
        Simpan dokumen ini sebagai bukti pembelian yang sah<br>
        Terima kasih atas kunjungan Anda
    </div>

</body>
</html>
