<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Produk</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #17202a;
            font-size: 11px;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }
        .subtitle {
            color: #657383;
            margin: 0 0 16px;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary td {
            border: 1px solid #d9e2ec;
            padding: 8px 12px;
            width: 20%;
        }
        .summary .label {
            display: block;
            color: #657383;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .summary .value {
            display: block;
            font-size: 14px;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th, table.data td {
            border: 1px solid #d9e2ec;
            padding: 6px 8px;
            text-align: left;
        }
        table.data th {
            background: #eef4f8;
            text-transform: uppercase;
            font-size: 9px;
            color: #657383;
        }
        .text-right { text-align: right; }
        .status-aman { color: #15803d; }
        .status-menipis { color: #b45309; }
        .status-habis { color: #b42318; }
    </style>
</head>
<body>
    <h1>Laporan Stok Produk</h1>
    <p class="subtitle">Periode: {{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($end)->translatedFormat('d M Y') }} &middot; Dicetak {{ now()->translatedFormat('d M Y H:i') }}</p>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Produk</span>
                <span class="value">{{ number_format($summary['total_products']) }}</span>
            </td>
            <td>
                <span class="label">Produk Aktif</span>
                <span class="value">{{ number_format($summary['active_products']) }}</span>
            </td>
            <td>
                <span class="label">Stok Menipis</span>
                <span class="value">{{ number_format($summary['low_stock_products']) }}</span>
            </td>
            <td>
                <span class="label">Stok Habis</span>
                <span class="value">{{ number_format($summary['out_of_stock_products']) }}</span>
            </td>
            <td>
                <span class="label">Pendapatan Periode</span>
                <span class="value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Min. Stok</th>
                <th>Status</th>
                <th class="text-right">Terjual</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                @php
                    $status = $product->stock_quantity <= 0 ? 'habis' : ($product->stock_quantity <= $product->min_stock ? 'menipis' : 'aman');
                @endphp
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category_name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($product->stock_quantity) }}</td>
                    <td class="text-right">{{ number_format($product->min_stock) }}</td>
                    <td class="status-{{ $status }}">{{ ucfirst($status) }}</td>
                    <td class="text-right">{{ number_format($product->quantity_sold) }}</td>
                    <td class="text-right">Rp {{ number_format($product->revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #657383;">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
