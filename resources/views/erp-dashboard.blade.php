<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP-POS Dashboard</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f7fb;
            --surface: #ffffff;
            --surface-soft: #eef4f8;
            --ink: #17202a;
            --muted: #657383;
            --line: #d9e2ec;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --accent: #d97706;
            --danger: #b91c1c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
        }

        .sidebar {
            background: #0b1720;
            color: #edf6f9;
            padding: 24px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 26px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: var(--primary);
            font-weight: 700;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }

        .brand-subtitle {
            margin-top: 3px;
            color: #a7b8c6;
            font-size: 12px;
        }

        .nav {
            display: grid;
            gap: 6px;
            margin-top: 24px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #c8d5df;
            font-size: 14px;
        }

        .nav a.active,
        .nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .content {
            min-width: 0;
            padding: 28px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 26px;
            line-height: 1.25;
        }

        .date {
            margin-top: 5px;
            color: var(--muted);
            font-size: 14px;
        }

        .status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--muted);
            font-size: 14px;
            white-space: nowrap;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 99px;
            background: #16a34a;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
        }

        .stat-value {
            margin-top: 8px;
            font-size: 26px;
            font-weight: 700;
        }

        .stat-note {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.65fr);
            gap: 18px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
        }

        .panel-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .panel-action {
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 700;
        }

        .module-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding: 16px;
        }

        .module {
            min-height: 116px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fbfdff;
        }

        .module:hover {
            border-color: var(--primary);
        }

        .module-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .module-name {
            font-weight: 700;
            font-size: 15px;
        }

        .module-desc {
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .badge {
            flex: 0 0 auto;
            padding: 4px 8px;
            border-radius: 999px;
            background: var(--surface-soft);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }

        .module-link {
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 700;
        }

        .activity {
            display: grid;
            gap: 0;
        }

        .activity-item {
            display: grid;
            grid-template-columns: 88px 1fr;
            gap: 12px;
            padding: 15px 18px;
            border-bottom: 1px solid var(--line);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-time {
            color: var(--muted);
            font-size: 12px;
        }

        .activity-title {
            font-weight: 700;
            font-size: 14px;
        }

        .activity-text {
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .quick-actions {
            display: grid;
            gap: 10px;
            padding: 16px;
        }

        .button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 46px;
            padding: 12px 14px;
            border-radius: 8px;
            background: var(--primary);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--ink);
        }

        .button.warning {
            background: var(--accent);
        }

        @media (max-width: 980px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                padding: 18px;
            }

            .nav {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .stats,
            .grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .content {
                padding: 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav,
            .stats,
            .grid,
            .module-list {
                grid-template-columns: 1fr;
            }

            .activity-item {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">EP</div>
                <div>
                    <div class="brand-title">ERP-POS</div>
                    <div class="brand-subtitle">Retail operations</div>
                </div>
            </div>

            <nav class="nav" aria-label="Main navigation">
                <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ url('/transactions') }}">Transaksi</a>
                <a href="{{ route('sales-notes') }}">Laporan Penjualan</a>
                <a href="{{ url('/products') }}">Produk</a>
                <a href="{{ route('jalankan-schedule-index') }}">Sync</a>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Dashboard ERP-POS</h1>
                    <div class="date">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
                <div class="status"><span class="dot"></span> Laravel aktif</div>
            </div>

            <section class="stats" aria-label="Ringkasan">
                <div class="stat">
                    <div class="stat-label">Fitur Tersedia</div>
                    <div class="stat-value">5</div>
                    <div class="stat-note">Termasuk fitur laporan penjualan</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Transaksi</div>
                    <div class="stat-value">Aktif</div>
                    <div class="stat-note">Endpoint data sudah tersedia</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Database</div>
                    <div class="stat-value">Siap</div>
                    <div class="stat-note">Migration sudah berjalan</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Status Web</div>
                    <div class="stat-value">Aktif</div>
                    <div class="stat-note">Laravel berjalan di port 8000</div>
                </div>
            </section>

            <div class="grid">
                <section class="panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Daftar Fitur</h2>
                        <a class="panel-action" href="{{ route('sales-notes') }}">Fitur kamu</a>
                    </div>
                    <div class="module-list">
                        <a class="module" href="{{ url('/transactions') }}">
                            <div class="module-top">
                                <div>
                                    <div class="module-name">Transaksi</div>
                                    <div class="module-desc">Melihat data transaksi yang tersimpan di database.</div>
                                </div>
                                <span class="badge">JSON</span>
                            </div>
                            <span class="module-link">Buka transaksi</span>
                        </a>

                        <a class="module" href="{{ url('/products') }}">
                            <div class="module-top">
                                <div>
                                    <div class="module-name">Produk</div>
                                    <div class="module-desc">Endpoint untuk mengambil data produk dari service lokal.</div>
                                </div>
                                <span class="badge">API</span>
                            </div>
                            <span class="module-link">Buka produk</span>
                        </a>

                        <a class="module" href="{{ route('jalankan-schedule-index') }}">
                            <div class="module-top">
                                <div>
                                    <div class="module-name">Sync Data</div>
                                    <div class="module-desc">Dipakai kalau data penjualan perlu disinkronkan dari proses lokal.</div>
                                </div>
                                <span class="badge">Command</span>
                            </div>
                            <span class="module-link">Buka sync</span>
                        </a>

                        <a class="module" href="{{ url('/transactions') }}">
                            <div class="module-top">
                                <div>
                                    <div class="module-name">Data Transaksi</div>
                                    <div class="module-desc">Sumber data JSON untuk detail barang, qty, harga, dan subtotal.</div>
                                </div>
                                <span class="badge">Source</span>
                            </div>
                            <span class="module-link">Cek data</span>
                        </a>

                        <a class="module" href="{{ route('sales-notes') }}">
                            <div class="module-top">
                                <div>
                                    <div class="module-name">Laporan Penjualan</div>
                                    <div class="module-desc">Fitur kamu untuk menampilkan hasil penjualan per transaksi.</div>
                                </div>
                                <span class="badge">Fitur Kamu</span>
                            </div>
                            <span class="module-link">Lihat laporan</span>
                        </a>
                    </div>
                </section>

                <aside class="panel">
                    <div class="panel-header">
                        <h2 class="panel-title">Aksi Laporan</h2>
                    </div>
                    <div class="quick-actions">
                        <a class="button" href="{{ route('sales-notes') }}">Lihat Laporan Penjualan <span>-></span></a>
                        <a class="button secondary" href="{{ route('jalankan-schedule-index') }}">Sync Data <span>-></span></a>
                        <a class="button warning" href="{{ url('/transactions') }}">Data Transaksi <span>-></span></a>
                    </div>

                    <div class="panel-header">
                        <h2 class="panel-title">Aktivitas</h2>
                    </div>
                    <div class="activity">
                        <div class="activity-item">
                            <div class="activity-time">Sekarang</div>
                            <div>
                                <div class="activity-title">Fitur laporan aktif</div>
                                <div class="activity-text">Route utama sudah mengarah ke halaman pengantar laporan penjualan.</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-time">Database</div>
                            <div>
                                <div class="activity-title">Migration siap</div>
                                <div class="activity-text">Tabel transaksi, detail transaksi, cache, dan coba tersedia.</div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
