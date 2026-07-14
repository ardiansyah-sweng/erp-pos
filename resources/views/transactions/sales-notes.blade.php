@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
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

    * { box-sizing: border-box; }

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

    a { color: inherit; text-decoration: none; }

    .page { max-width: 1180px; margin: 0 auto; padding: 28px; }

    .hero {
        display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center;
        margin-bottom: 20px; padding: 22px; border: 1px solid rgba(15, 118, 110, 0.18);
        border-radius: 8px; background: var(--hero-bg); box-shadow: 0 18px 45px var(--shadow);
    }

    html[data-theme="dark"] .hero { border-color: var(--line); backdrop-filter: blur(18px); }

    .eyebrow {
        display: inline-flex; align-items: center; min-height: 26px; margin-bottom: 10px;
        padding: 5px 9px; border-radius: 999px; background: #e5f4f1;
        color: var(--primary-dark); font-size: 12px; font-weight: 700;
    }

    html[data-theme="dark"] .eyebrow,
    html[data-theme="dark"] .note-number { background: rgba(34, 211, 238, 0.12); }

    .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; }

    .back {
        display: inline-flex; align-items: center; justify-content: center; min-height: 38px;
        padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px;
        background: var(--surface); color: var(--primary-dark); font-size: 14px; font-weight: 700;
    }

    html[data-theme="dark"] .back { background: rgba(2, 6, 23, 0.72); color: #cbd5e1; }

    .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }

    .download {
        display: inline-flex; align-items: center; justify-content: center; min-height: 38px;
        padding: 9px 14px; border-radius: 8px; background: var(--primary);
        color: #ffffff; font-size: 14px; font-weight: 700;
    }

    .download:hover { background: var(--primary-dark); }

    h1 { margin: 0; font-size: 30px; line-height: 1.25; }
    .subtitle { margin-top: 6px; color: var(--muted); font-size: 14px; }

    .summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
    .summary-card, .empty, .note { border: 1px solid var(--line); border-radius: 8px; background: var(--surface); }
    html[data-theme="dark"] .summary-card, html[data-theme="dark"] .empty, html[data-theme="dark"] .note { backdrop-filter: blur(18px); }

    .summary-card { position: relative; overflow: hidden; padding: 18px; box-shadow: 0 10px 28px var(--shadow); }
    .summary-card::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--primary); }
    .summary-label { color: var(--muted); font-size: 13px; }
    .summary-value { margin-top: 8px; font-size: 24px; font-weight: 700; }
    .summary-note { margin-top: 7px; color: var(--muted); font-size: 12px; }

    .section-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 22px 0 12px; }
    .section-heading h2 { margin: 0; font-size: 18px; }
    .section-meta { color: var(--muted); font-size: 13px; }

    .notes { display: grid; gap: 14px; }
    .note { overflow: hidden; box-shadow: 0 10px 28px var(--shadow); }

    .note-header {
        display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px;
        padding: 18px; border-bottom: 1px solid var(--line); background: var(--note-head);
    }

    .note-number {
        display: inline-flex; align-items: center; min-height: 30px; padding: 5px 10px;
        border-radius: 999px; background: #e5f4f1; color: var(--primary-dark);
        font-size: 17px; font-weight: 700;
    }

    .note-date { margin-top: 5px; color: var(--muted); font-size: 13px; }
    .note-total { text-align: right; }
    .note-total-label { color: var(--muted); font-size: 12px; }
    .note-total-value { margin-top: 5px; color: var(--primary-dark); font-size: 20px; font-weight: 700; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 18px; border-bottom: 1px solid var(--line); text-align: left; font-size: 14px; }
    th { background: var(--soft); color: var(--muted); font-size: 12px; letter-spacing: 0; text-transform: uppercase; }
    tbody tr:hover { background: var(--row-hover); }
    tr:last-child td { border-bottom: 0; }
    .number { text-align: right; white-space: nowrap; }

    .empty { padding: 42px 28px; text-align: center; box-shadow: 0 10px 28px var(--shadow); }
    .empty-title { font-size: 18px; font-weight: 700; }
    .empty-text { max-width: 520px; margin: 8px auto 0; color: var(--muted); font-size: 14px; line-height: 1.5; }

    @media (max-width: 760px) {
        .page { padding: 18px; }
        .hero, .topbar, .note-header { align-items: flex-start; grid-template-columns: 1fr; flex-direction: column; }
        .summary { grid-template-columns: 1fr; }
        .note-total { text-align: left; }
    }
</style>
@endpush

@section('content')
<main class="page">
    <section class="hero">
        <div class="topbar">
            <div>
                <div class="eyebrow">Fitur laporan</div>
                <h1>Laporan Penjualan</h1>
                <div class="subtitle">Setiap transaksi menampilkan total penjualan dan rincian barang yang terjual.</div>
            </div>
        </div>
        @if (!($isPdf ?? false))
            <div class="actions">
                <a class="download" href="{{ route('sales-notes.pdf', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}">Download PDF</a>
                <a class="back" href="{{ route('pos.index') }}">Kembali POS</a>
            </div>
        @endif
    </section>

    @if (!($isPdf ?? false))
        <form method="GET" action="{{ route('sales-notes') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:18px; padding:16px; border:1px solid var(--line); border-radius:8px; background:var(--surface); box-shadow:0 10px 28px var(--shadow);">
            <div>
                <label for="start_date" style="display:block; margin-bottom:6px; font-size:12px; color:var(--muted);">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" style="padding:8px 10px; border:1px solid var(--line); border-radius:6px; background:var(--surface); color:var(--ink);">
            </div>
            <div>
                <label for="end_date" style="display:block; margin-bottom:6px; font-size:12px; color:var(--muted);">Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" style="padding:8px 10px; border:1px solid var(--line); border-radius:6px; background:var(--surface); color:var(--ink);">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="download" style="border:0; cursor:pointer;">Tampilkan</button>
                <a class="back" href="{{ route('sales-notes') }}">Reset</a>
            </div>
        </form>

        @if ($startDate || $endDate)
            <div class="summary-note" style="margin-bottom:16px;">Filter aktif: {{ $startDate ? 'dari ' . $startDate : 'semua data' }}{{ $endDate ? ' sampai ' . $endDate : '' }}</div>
        @endif
    @endif

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
                                    <tr><td colspan="4">Belum ada detail barang untuk transaksi ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
    try {
        document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
    } catch (error) {
        document.documentElement.dataset.theme = 'dark';
    }
</script>
@endpush
