<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
        } catch (error) {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <title>Laporan Penjualan</title>
    <style>
        :root {
            --bg: #f3f6f8;
            --surface: #ffffff;
            --ink: #17202a;
            --muted: #657383;
            --line: #d9e2ec;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --soft: #eef4f8;
            --accent: #b45309;
            --success: #15803d;
            --hero-bg: #ffffff;
            --note-head: #fbfdff;
            --row-hover: #fbfdff;
            --shadow: rgba(23, 32, 42, 0.08);
        }

        html[data-theme="dark"] {
            --bg: #020617;
            --surface: rgba(15, 23, 42, 0.78);
            --ink: #f8fafc;
            --muted: #94a3b8;
            --line: rgba(255, 255, 255, 0.1);
            --primary: #22d3ee;
            --primary-dark: #67e8f9;
            --soft: rgba(15, 23, 42, 0.92);
            --accent: #f59e0b;
            --success: #34d399;
            --hero-bg: rgba(255, 255, 255, 0.05);
            --note-head: rgba(15, 23, 42, 0.7);
            --row-hover: rgba(34, 211, 238, 0.08);
            --shadow: rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #e8f1f1 0, var(--bg) 280px);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        html[data-theme="dark"] body {
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.24), transparent 34rem),
                radial-gradient(circle at top center, rgba(34, 211, 238, 0.16), transparent 30rem),
                var(--bg);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
            margin-bottom: 20px;
            padding: 22px;
            border: 1px solid rgba(15, 118, 110, 0.18);
            border-radius: 8px;
            background: var(--hero-bg);
            box-shadow: 0 18px 45px var(--shadow);
        }

        html[data-theme="dark"] .hero {
            border-color: var(--line);
            backdrop-filter: blur(18px);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            margin-bottom: 10px;
            padding: 5px 9px;
            border-radius: 999px;
            background: #e5f4f1;
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 700;
        }

        html[data-theme="dark"] .eyebrow,
        html[data-theme="dark"] .note-number {
            background: rgba(34, 211, 238, 0.12);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--primary-dark);
            font-size: 14px;
            font-weight: 700;
        }

        .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--primary-dark);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        html[data-theme="dark"] .back,
        html[data-theme="dark"] .theme-toggle {
            background: rgba(2, 6, 23, 0.72);
            color: #cbd5e1;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 9px 14px;
            border-radius: 8px;
            background: var(--primary);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .download:hover {
            background: var(--primary-dark);
        }

        h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.25;
        }

        .subtitle {
            margin-top: 6px;
            color: var(--muted);
            font-size: 14px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .summary-card,
        .empty,
        .note {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
        }

        html[data-theme="dark"] .summary-card,
        html[data-theme="dark"] .empty,
        html[data-theme="dark"] .note {
            backdrop-filter: blur(18px);
        }

        .summary-card {
            position: relative;
            overflow: hidden;
            padding: 18px;
            box-shadow: 0 10px 28px var(--shadow);
        }

        .summary-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--primary);
        }

        .summary-label {
            color: var(--muted);
            font-size: 13px;
        }

        .summary-value {
            margin-top: 8px;
            font-size: 24px;
            font-weight: 700;
        }

        .summary-note {
            margin-top: 7px;
            color: var(--muted);
            font-size: 12px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 22px 0 12px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 18px;
        }

        .section-meta {
            color: var(--muted);
            font-size: 13px;
        }

        .notes {
            display: grid;
            gap: 14px;
        }

        .note {
            overflow: hidden;
            box-shadow: 0 10px 28px var(--shadow);
        }

        .note-header {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            padding: 18px;
            border-bottom: 1px solid var(--line);
            background: var(--note-head);
        }

        .note-number {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #e5f4f1;
            color: var(--primary-dark);
            font-size: 17px;
            font-weight: 700;
        }

        .note-date {
            margin-top: 5px;
            color: var(--muted);
            font-size: 13px;
        }

        .note-total {
            text-align: right;
        }

        .note-total-label {
            color: var(--muted);
            font-size: 12px;
        }

        .note-total-value {
            margin-top: 5px;
            color: var(--primary-dark);
            font-size: 20px;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 18px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 14px;
        }

        th {
            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: var(--row-hover);
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .empty {
            padding: 42px 28px;
            text-align: center;
            box-shadow: 0 10px 28px var(--shadow);
        }

        .empty-title {
            font-size: 18px;
            font-weight: 700;
        }

        .empty-text {
            max-width: 520px;
            margin: 8px auto 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            .page {
                padding: 18px;
            }

            .hero,
            .topbar,
            .note-header {
                align-items: flex-start;
                grid-template-columns: 1fr;
                flex-direction: column;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .note-total {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="hero">
            <div class="topbar">
                <div>
                    <div class="eyebrow">Fitur laporan</div>
                    <h1>Laporan Penjualan</h1>
                    <div class="subtitle">Setiap transaksi menampilkan total penjualan dan rincian barang yang terjual.</div>
                </div>
            </div>
            <div class="actions">
                <a class="download" href="{{ route('sales-notes.pdf') }}">Download PDF</a>
                <button id="themeToggle" class="theme-toggle" type="button" aria-pressed="false">
                    <span id="themeIcon" aria-hidden="true"></span>
                    <span id="themeLabel">Mode terang</span>
                </button>
                <a class="back" href="{{ route('pos.index') }}">Kembali POS</a>
            </div>
        </section>

        <section class="summary" aria-label="Ringkasan penjualan">
            <div class="summary-card">
                <div class="summary-label">Jumlah Transaksi</div>
                <div class="summary-value">{{ $transactions->count() }}</div>
                <div class="summary-note">Total transaksi tercatat</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Penjualan</div>
                <div class="summary-value">Rp{{ number_format($transactions->sum('total'), 0, ',', '.') }}</div>
                <div class="summary-note">Akumulasi semua transaksi</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Item Terjual</div>
                <div class="summary-value">{{ $transactions->sum('item_count') }}</div>
                <div class="summary-note">Berdasarkan quantity detail</div>
            </div>
        </section>

        @if ($transactions->isEmpty())
            <section class="empty">
                <div class="empty-title">Belum ada data penjualan</div>
                <div class="empty-text">Data transaksi masih kosong. Setelah transaksi dan detail transaksi tersimpan, daftar penjualan akan muncul di halaman ini.</div>
            </section>
        @else
            <div class="section-heading">
                <h2>Daftar Transaksi</h2>
                <div class="section-meta">{{ $transactions->count() }} transaksi</div>
            </div>
            <section class="notes" aria-label="Daftar laporan penjualan">
                @foreach ($transactions as $transaction)
                    <article class="note">
                        <header class="note-header">
                            <div>
                                <div class="note-number">{{ $transaction['transaction_code'] }}</div>
                                <div class="note-date">
                                    {{ $transaction['date'] ? $transaction['date']->translatedFormat('d F Y H:i') : 'Tanggal belum tersedia' }}
                                    &middot; {{ $transaction['item_count'] }} item
                                </div>
                            </div>
                            <div class="note-total">
                                <div class="note-total-label">Total Transaksi</div>
                                <div class="note-total-value">Rp{{ number_format($transaction['total'], 0, ',', '.') }}</div>
                            </div>
                        </header>

                        <div class="table-wrap">
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
                                    @forelse ($transaction['details'] as $detail)
                                        <tr>
                                            <td>{{ $detail->product_id }}</td>
                                            <td class="number">{{ $detail->quantity }}</td>
                                            <td class="number">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                                            <td class="number">Rp{{ number_format($detail->amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Belum ada detail barang untuk transaksi ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
    <script>
        const themeStorageKey = 'erp-pos-theme';
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeLabel = document.getElementById('themeLabel');
        const themeIcons = {
            sun: `
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path>
                    <path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path>
                    <path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path>
                    <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
            `,
            moon: `
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 7.5A9 9 0 1 1 12 3Z"></path>
                </svg>
            `,
        };

        const applyTheme = (theme) => {
            document.documentElement.dataset.theme = theme;
            themeIcon.innerHTML = theme === 'light' ? themeIcons.moon : themeIcons.sun;
            themeLabel.textContent = theme === 'light' ? 'Mode gelap' : 'Mode terang';
            themeToggle.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');

            try {
                localStorage.setItem(themeStorageKey, theme);
            } catch (error) {
                console.error(error);
            }
        };

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.dataset.theme === 'light' ? 'light' : 'dark';
            applyTheme(currentTheme === 'light' ? 'dark' : 'light');
        });

        applyTheme(document.documentElement.dataset.theme === 'light' ? 'light' : 'dark');
    </script>
</body>
</html>
