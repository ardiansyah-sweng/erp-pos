<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Stok Opname - {{ now()->format('d-m-Y') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 16px;
        }

        .button {
            display: inline-block;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: #0f172a;
            padding: 10px 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            border-color: #0891b2;
            background: #0891b2;
            color: #fff;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 10px 30px rgb(15 23 42 / 12%);
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 22px;
        }

        .muted {
            color: #475569;
        }

        .meta {
            min-width: 220px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #64748b;
            padding: 7px 6px;
            vertical-align: middle;
        }

        th {
            background: #e2e8f0;
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .number {
            width: 32px;
            text-align: center;
        }

        .quantity {
            width: 72px;
            text-align: center;
        }

        .write-cell {
            height: 32px;
        }

        .empty {
            padding: 32px;
            text-align: center;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            margin-top: 40px;
            text-align: center;
        }

        .signature-space {
            height: 64px;
        }

        .signature-line {
            border-top: 1px solid #0f172a;
            padding-top: 6px;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <a href="{{ route('stock-adjustments.index') }}" class="button">Kembali</a>
        <button type="button" class="button button-primary" onclick="window.print()">Cetak Form</button>
    </div>

    <main class="sheet">
        <header class="header">
            <div>
                <h1>Form Stok Opname</h1>
                <div class="muted">{{ config('app.name', 'ERP POS') }}</div>
            </div>
            <div class="meta">
                <div class="meta-row"><span>Tanggal</span><strong>{{ now()->translatedFormat('d F Y') }}</strong></div>
                <div class="meta-row"><span>Petugas</span><strong>________________</strong></div>
                <div class="meta-row"><span>Lokasi</span><strong>________________</strong></div>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th class="number">No.</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Satuan</th>
                    <th class="quantity">Stok Sistem</th>
                    <th class="quantity">Stok Fisik</th>
                    <th class="quantity">Selisih</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="number">{{ $loop->iteration }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="quantity">{{ $product->unit }}</td>
                        <td class="quantity">{{ $product->stock_quantity }}</td>
                        <td class="write-cell"></td>
                        <td class="write-cell"></td>
                        <td class="write-cell"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">Belum ada produk aktif untuk diperiksa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <section class="signatures">
            <div>
                <div>Dihitung oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line">Petugas</div>
            </div>
            <div>
                <div>Diperiksa oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line">Supervisor</div>
            </div>
            <div>
                <div>Disetujui oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line">Penanggung Jawab</div>
            </div>
        </section>
    </main>
</body>
</html>
