<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP POS</title>
    <style>
        :root {
            --bg: #f5efe6;
            --panel: rgba(255, 252, 247, 0.9);
            --line: #d9c9b7;
            --ink: #1f1a17;
            --muted: #6c625b;
            --accent: #0f766e;
            --accent-2: #c2410c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(194, 65, 12, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 118, 110, 0.15), transparent 30%),
                linear-gradient(135deg, #f8f3ec 0%, #efe4d4 100%);
        }

        .shell {
            max-width: 1120px;
            margin: 0 auto;
            padding: 40px 20px 56px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .95fr;
            gap: 24px;
            align-items: stretch;
        }

        .panel {
            background: var(--panel);
            border: 1px solid rgba(217, 201, 183, 0.9);
            border-radius: 28px;
            box-shadow: 0 20px 60px rgba(73, 52, 33, 0.08);
            backdrop-filter: blur(10px);
        }

        .hero-copy {
            padding: 36px;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            font-size: 13px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.55);
        }

        h1 {
            margin: 0;
            font-size: clamp(38px, 6vw, 72px);
            line-height: .95;
            letter-spacing: -.04em;
        }

        .lead {
            margin: 20px 0 0;
            max-width: 54ch;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #fffaf4;
            background: linear-gradient(135deg, var(--accent), #155e75);
            box-shadow: 0 14px 30px rgba(15, 118, 110, 0.22);
        }

        .btn-secondary {
            color: var(--ink);
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--line);
        }

        .status-card {
            padding: 28px;
            display: grid;
            gap: 16px;
        }

        .status-grid {
            display: grid;
            gap: 12px;
        }

        .status-item {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(217, 201, 183, 0.75);
        }

        .status-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .status-item span {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .section {
            margin-top: 24px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            padding: 24px;
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .card p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.7;
        }

        .card a {
            color: var(--accent-2);
            font-weight: 700;
            text-decoration: none;
        }

        .foot {
            margin-top: 18px;
            color: var(--muted);
            font-size: 14px;
            text-align: center;
        }

        code {
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(31, 26, 23, 0.07);
            font-size: 13px;
        }

        @media (max-width: 860px) {
            .hero,
            .section {
                grid-template-columns: 1fr;
            }

            .hero-copy,
            .status-card,
            .card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div class="panel hero-copy">
                <div class="eyebrow">ERP POS Local Workspace</div>
                <h1>ERP POS siap dijalankan dari halaman utama.</h1>
                <p class="lead">
                    Halaman ini menggantikan welcome screen bawaan Laravel. Dari sini Anda bisa langsung membuka endpoint data, halaman schedule, dan memastikan project berjalan sesuai alur dokumen instalasi.
                </p>
                <div class="actions">
                    <a class="btn btn-primary" href="/jalankan-schedule">Buka Schedule</a>
                    <a class="btn btn-secondary" href="/products">Lihat Produk</a>
                    <a class="btn btn-secondary" href="/api/products">Cek API Produk</a>
                </div>
            </div>

            <aside class="panel status-card">
                <div>
                    <div class="eyebrow">Quick Check</div>
                    <h2 style="margin:0;font-size:28px;">Status pengembangan</h2>
                </div>
                <div class="status-grid">
                    <div class="status-item">
                        <strong>Database seed</strong>
                        <span>Seeder sudah bisa dijalankan langsung setelah <code>php artisan migrate</code>.</span>
                    </div>
                    <div class="status-item">
                        <strong>Endpoint lokal</strong>
                        <span><code>/products</code>, <code>/prices</code>, dan <code>/api/products</code> sudah aktif.</span>
                    </div>
                    <div class="status-item">
                        <strong>Halaman root</strong>
                        <span>Tidak lagi menampilkan template default Laravel.</span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="section">
            <article class="panel card">
                <h2>Produk</h2>
                <p>Endpoint ringan untuk kebutuhan lokal dan pengujian data produk sederhana.</p>
                <a href="/products">Buka `/products`</a>
            </article>

            <article class="panel card">
                <h2>Harga</h2>
                <p>Katalog harga dummy agar alur pengembangan dan seed tidak bergantung server eksternal lain.</p>
                <a href="/prices">Buka `/prices`</a>
            </article>

            <article class="panel card">
                <h2>API</h2>
                <p>Format respons yang lebih dekat dengan dokumentasi API project untuk kebutuhan integrasi.</p>
                <a href="/api/products">Buka `/api/products`</a>
            </article>
        </section>

        <div class="foot">
            ERP POS development page • Laravel {{ Illuminate\Foundation\Application::VERSION }} • PHP {{ PHP_VERSION }}
        </div>
    </div>
</body>
</html>
