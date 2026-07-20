@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #0d1117;
        color: #e6edf3;
        min-height: 100vh;
    }

    .header {
        background: #161b22;
        border-bottom: 1px solid #21262d;
        padding: 24px 32px;
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }
    .header-label {
        color: #2dd4bf;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .header-title {
        font-size: 28px;
        font-weight: 700;
        color: #f0f6fc;
    }
    .header-sub {
        font-size: 13px;
        color: #8b949e;
        margin-top: 4px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #21262d;
        border: 1px solid #30363d;
        color: #e6edf3;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        transition: background 0.2s;
    }
    .btn-back:hover { background: #30363d; }

    .main { padding: 28px 32px; max-width: 1200px; margin: 0 auto; }

    .flatpickr-calendar {
        background: #161b22 !important;
        border: 1px solid #21262d !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important;
    }
    .flatpickr-months .flatpickr-month,
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-weekdays { background: #161b22 !important; color: #e6edf3 !important; }
    .flatpickr-weekday { color: #8b949e !important; }
    .flatpickr-day { color: #e6edf3 !important; }
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay { color: #484f58 !important; }
    .flatpickr-day:hover { background: #21262d !important; }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: #2dd4bf !important;
        border-color: #2dd4bf !important;
        color: #0d1117 !important;
    }
    .flatpickr-day.inRange {
        background: #2dd4bf22 !important;
        border-color: #2dd4bf22 !important;
        box-shadow: -5px 0 0 #2dd4bf22, 5px 0 0 #2dd4bf22 !important;
    }
    .flatpickr-current-month input.cur-year { color: #e6edf3 !important; }
    .flatpickr-prev-month, .flatpickr-next-month { color: #8b949e !important; fill: #8b949e !important; }
    span.flatpickr-weekday { color: #8b949e !important; }

    .filter-bar {
        background: #161b22;
        border: 1px solid #21262d;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-label {
        font-size: 12px;
        color: #8b949e;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-right: 4px;
    }
    .filter-btn {
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid #30363d;
        background: #21262d;
        color: #8b949e;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #2dd4bf22;
        border-color: #2dd4bf;
        color: #2dd4bf;
    }
    .filter-divider {
        width: 1px;
        height: 24px;
        background: #21262d;
        margin: 0 4px;
    }
    .filter-custom {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-input {
        background: #21262d;
        border: 1px solid #30363d;
        color: #e6edf3;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 13px;
    }
    .filter-input:focus { outline: none; border-color: #2dd4bf; }
    .btn-apply {
        background: #2dd4bf;
        color: #0d1117;
        border: none;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-apply:hover { opacity: 0.85; }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .card {
        background: #161b22;
        border: 1px solid #21262d;
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--accent);
    }
    .card-icon { font-size: 22px; margin-bottom: 12px; }
    .card-label {
        font-size: 11px;
        color: #8b949e;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
    }
    .card-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--accent);
        margin-top: 4px;
        line-height: 1.1;
    }
    .card-sub { font-size: 12px; color: #8b949e; margin-top: 4px; }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }

    .panel {
        background: #161b22;
        border: 1px solid #21262d;
        border-radius: 12px;
        overflow: hidden;
    }
    .panel-header {
        padding: 16px 20px;
        border-bottom: 1px solid #21262d;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .panel-title { font-size: 14px; font-weight: 700; color: #f0f6fc; }
    .panel-badge {
        font-size: 11px;
        background: #2dd4bf22;
        color: #2dd4bf;
        padding: 3px 9px;
        border-radius: 20px;
        border: 1px solid #2dd4bf44;
    }
    .panel-body { padding: 20px; }
    .chart-wrap { position: relative; height: 220px; }

    .top-product-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #21262d;
    }
    .top-product-item:last-child { border-bottom: none; }
    .rank {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #21262d;
        color: #8b949e;
        font-size: 12px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .rank-1 { background: #f59e0b22; color: #f59e0b; border: 1px solid #f59e0b44; }
    .rank-2 { background: #94a3b822; color: #94a3b8; border: 1px solid #94a3b844; }
    .rank-3 { background: #cd7f3222; color: #cd7f32; border: 1px solid #cd7f3244; }
    .product-info { flex: 1; min-width: 0; }
    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: #f0f6fc;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-qty { font-size: 12px; color: #8b949e; margin-top: 2px; }
    .product-total {
        font-size: 13px;
        font-weight: 700;
        color: #2dd4bf;
        flex-shrink: 0;
    }

    .table-wrap {
        background: #161b22;
        border: 1px solid #21262d;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #0d1117;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #8b949e;
        text-align: left;
        border-bottom: 1px solid #21262d;
    }
    tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: #c9d1d9;
        border-bottom: 1px solid #21262d11;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #21262d55; }

    .pagination-wrap {
        padding: 14px 20px;
        border-top: 1px solid #21262d;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #8b949e;
    }
    .pagination-wrap a, .pagination-wrap span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 30px; height: 30px;
        padding: 0 8px;
        border-radius: 6px;
        border: 1px solid #30363d;
        background: #21262d;
        color: #8b949e;
        text-decoration: none;
        font-size: 12px;
        margin: 0 2px;
        transition: all 0.2s;
    }
    .pagination-wrap a:hover { border-color: #2dd4bf; color: #2dd4bf; }
    .pagination-wrap span[aria-current] { background: #2dd4bf22; border-color: #2dd4bf; color: #2dd4bf; }

    .empty-state { padding: 40px; text-align: center; color: #8b949e; font-size: 14px; }
</style>
@endpush

@section('content')

<div class="header">
    <div class="header-top">
        <div>
            <div class="header-label">Laporan</div>
            <div class="header-title">Laporan Penjualan</div>
            <div class="header-sub">
                {{ \Carbon\Carbon::parse($start)->format('d M Y') }} —
                {{ \Carbon\Carbon::parse($end)->format('d M Y H:i') }}
            </div>
        </div>
        <a href="{{ url('/pos') }}" class="btn-back">← Kembali ke POS</a>
    </div>
</div>

<div class="main">

    <form method="GET" action="{{ route('reports.index') }}">
        <div class="filter-bar">
            <span class="filter-label">Filter:</span>
            @foreach(['today' => 'Hari ini', 'yesterday' => 'Kemarin', 'this_week' => 'Minggu ini', 'this_month' => 'Bulan ini'] as $key => $label)
                <a href="{{ route('reports.index', ['filter' => $key]) }}"
                   class="filter-btn {{ $filter === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach

            <div class="filter-divider"></div>

            <div class="filter-custom">
                <input type="hidden" name="filter" value="custom">
                <input type="text" id="start_date" name="start_date" class="filter-input datepicker"
                       value="{{ $filter === 'custom' ? $startDate : '' }}"
                       placeholder="Dari tanggal" autocomplete="off" readonly>
                <span style="color:#8b949e; font-size:12px;">—</span>
                <input type="text" id="end_date" name="end_date" class="filter-input datepicker"
                       value="{{ $filter === 'custom' ? $endDate : '' }}"
                       placeholder="Sampai tanggal" autocomplete="off" readonly>
                <button type="submit" class="btn-apply">Terapkan</button>
            </div>
        </div>
    </form>

    <div class="cards">
        <div class="card" style="--accent: #2dd4bf">
            <div class="card-icon">🧾</div>
            <div class="card-label">Total Transaksi</div>
            <div class="card-value">{{ number_format($summary['total_transaksi']) }}</div>
            <div class="card-sub">transaksi berhasil</div>
        </div>
        <div class="card" style="--accent: #22c55e">
            <div class="card-icon">💰</div>
            <div class="card-label">Total Pendapatan</div>
            <div class="card-value">Rp{{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="card-sub">setelah diskon</div>
        </div>
        <div class="card" style="--accent: #3b82f6">
            <div class="card-icon">📊</div>
            <div class="card-label">Rata-rata / Transaksi</div>
            <div class="card-value">Rp{{ number_format($summary['rata_rata'], 0, ',', '.') }}</div>
            <div class="card-sub">nilai rata-rata</div>
        </div>
        <div class="card" style="--accent: #f59e0b">
            <div class="card-icon">📅</div>
            <div class="card-label">Periode</div>
            <div class="card-value" style="font-size:16px; margin-top:8px;">
                {{ \Carbon\Carbon::parse($start)->format('d M') }} —
                {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
            </div>
            <div class="card-sub">rentang laporan</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Grafik Pendapatan Harian</span>
                <span class="panel-badge">{{ count($chartData['labels']) }} hari</span>
            </div>
            <div class="panel-body">
                <div class="chart-wrap">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Produk Terlaris</span>
                <span class="panel-badge">Top 5</span>
            </div>
            <div class="panel-body">
                @forelse($topProducts as $i => $product)
                    <div class="top-product-item">
                        <div class="rank rank-{{ $i + 1 }}">{{ $i + 1 }}</div>
                        <div class="product-info">
                            <div class="product-name">{{ $product->nama_produk }}</div>
                            <div class="product-qty">{{ number_format($product->total_qty) }} unit terjual</div>
                        </div>
                        <div class="product-total">
                            Rp{{ number_format($product->total_penjualan, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data produk.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <div class="panel-header">
            <span class="panel-title">Detail Transaksi</span>
            <span class="panel-badge">{{ $transactions->total() }} transaksi</span>
        </div>

        @if($transactions->count())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trx)
                <tr>
                    <td style="color:#8b949e; font-size:12px;">{{ $trx->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</td>
                    <td style="color:#2dd4bf; font-weight:700;">
                        Rp{{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }}</span>
            <div>{{ $transactions->links() }}</div>
        </div>
        @else
            <div class="empty-state">Belum ada transaksi pada periode ini.</div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr('#start_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        maxDate: 'today',
        locale: { firstDayOfWeek: 1 }
    });
    flatpickr('#end_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        maxDate: 'today',
        locale: { firstDayOfWeek: 1 }
    });

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: 'rgba(45, 212, 191, 0.2)',
                borderColor: '#2dd4bf',
                borderWidth: 2,
                borderRadius: 6,
                pointBackgroundColor: '#2dd4bf',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#161b22',
                    borderColor: '#21262d',
                    borderWidth: 1,
                    titleColor: '#8b949e',
                    bodyColor: '#2dd4bf',
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#21262d' },
                    ticks: { color: '#8b949e', font: { size: 11 } }
                },
                y: {
                    grid: { color: '#21262d' },
                    ticks: {
                        color: '#8b949e',
                        font: { size: 11 },
                        callback: val => 'Rp' + (val >= 1000000
                            ? (val/1000000).toFixed(1)+'jt'
                            : val.toLocaleString('id-ID'))
                    }
                }
            }
        }
    });
</script>
@endpush
