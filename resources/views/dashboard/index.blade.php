@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    html[data-theme="light"] body {
        background: #f6f8fb !important;
        color: #0f172a !important;
    }
    html[data-theme="light"] .bg-white\/5,
    html[data-theme="light"] .bg-slate-950\/60,
    html[data-theme="light"] .bg-slate-950\/70,
    html[data-theme="light"] .bg-slate-900\/80 {
        background-color: #ffffff !important;
    }
    html[data-theme="light"] .border-white\/10 {
        border-color: #dbe3ea !important;
    }
    html[data-theme="light"] .text-white {
        color: #0f172a !important;
    }
    html[data-theme="light"] .text-slate-200,
    html[data-theme="light"] .text-slate-300,
    html[data-theme="light"] .text-slate-400 {
        color: #64748b !important;
    }
    html[data-theme="light"] .text-emerald-300 {
        color: #047857 !important;
    }
    html[data-theme="light"] .text-cyan-300\/80 {
        color: #0e7490 !important;
    }
    html[data-theme="light"] .text-amber-300 {
        color: #b45309 !important;
    }
    html[data-theme="light"] .text-rose-300 {
        color: #e11d48 !important;
    }
    html[data-theme="light"] #liveClock {
        color: #0369a1 !important;
    }
</style>
@endpush

@section('content')

<div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

<main class="relative mx-auto max-w-7xl px-4 py-6 lg:px-8">

    {{-- HERO --}}
    <section class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Dashboard</p>
                <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Halo, {{ session('cashier_name', 'Admin') }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-300">
                    Ringkasan penjualan hari ini, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-4xl font-bold text-cyan-300" id="liveClock">--:--:--</div>
                    <div class="text-xs text-slate-400">WIB</div>
                </div>
                <a href="{{ route('pos.index') }}" class="rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                    Buka POS →
                </a>
            </div>
        </div>
    </section>

    {{-- SUMMARY CARDS --}}
    <section class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-4">
        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Pendapatan</span>
            </div>
            <div class="mt-1 text-2xl font-bold text-emerald-300">
                Rp{{ number_format($summary['revenue'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Hari ini</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Transaksi</span>
            </div>
            <div class="mt-1 text-2xl font-bold text-white">{{ $summary['count'] }}</div>
            <div class="mt-1 text-xs text-slate-500">transaksi hari ini</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Item Terjual</span>
            </div>
            <div class="mt-1 text-2xl font-bold text-cyan-300">{{ $summary['items'] }}</div>
            <div class="mt-1 text-xs text-slate-500">unit terjual hari ini</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Kasir</span>
            </div>
            <div class="mt-1 text-2xl font-bold text-white truncate">{{ session('cashier_name', 'Admin') }}</div>
            <div class="mt-1 text-xs text-slate-500">sedang online</div>
        </div>
    </section>

    {{-- CHART + LOW STOCK --}}
    <section class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div class="lg:col-span-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Penjualan 7 Hari Terakhir</h2>
                <a href="{{ route('reports.index') }}" class="text-xs text-cyan-300 hover:underline">Lihat detail →</a>
            </div>
            <div class="relative h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <h2 class="mb-3 text-sm font-semibold text-white">Stok Menipis</h2>
            @forelse($lowStock as $p)
                <div class="flex items-center justify-between border-b border-white/5 py-2 last:border-0">
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-medium text-white">{{ $p->name }}</div>
                        <div class="text-xs text-slate-400">{{ $p->sku }} · Min {{ $p->min_stock }}</div>
                    </div>
                    <span class="{{ $p->stock_quantity == 0 ? 'bg-rose-400/20 text-rose-300' : 'bg-amber-400/20 text-amber-300' }} ml-2 shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                        {{ $p->stock_quantity }}
                    </span>
                </div>
            @empty
                <div class="py-6 text-center text-sm text-slate-400">
                    Semua stok aman
                </div>
            @endforelse
            @if($lowStock->isNotEmpty())
                <a href="{{ route('stock-adjustments.index') }}" class="mt-3 inline-block text-xs text-cyan-300 hover:underline">Atur stok →</a>
            @endif
        </div>
    </section>

    {{-- BOTTOM GRID: TOP PRODUCTS + RECENT TRANSACTIONS + QUICK ACTIONS --}}
    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        {{-- Top Products --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <h2 class="mb-3 text-sm font-semibold text-white">Produk Terlaris</h2>
            @forelse($topProducts as $i => $p)
                <div class="flex items-center gap-3 border-b border-white/5 py-2 last:border-0">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
                        {{ $i == 0 ? 'bg-amber-400/20 text-amber-300' : ($i == 1 ? 'bg-slate-400/20 text-slate-300' : ($i == 2 ? 'bg-orange-400/20 text-orange-300' : 'bg-white/5 text-slate-400')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-medium text-white">{{ $p->name }}</div>
                        <div class="text-xs text-slate-400">{{ $p->total_qty }}x terjual</div>
                    </div>
                    <div class="shrink-0 text-sm font-semibold text-emerald-300">
                        Rp{{ number_format($p->total_amount, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-sm text-slate-400">Belum ada data penjualan.</div>
            @endforelse
        </div>

        {{-- Recent Transactions --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Transaksi Terbaru</h2>
                <a href="{{ route('transactions.index') }}" class="text-xs text-cyan-300 hover:underline">Lihat semua →</a>
            </div>
            @forelse($recent as $t)
                <div class="flex items-center justify-between border-b border-white/5 py-2 last:border-0">
                    <div>
                        <div class="text-sm font-medium text-white">{{ $t['code'] }}</div>
                        <div class="text-xs text-slate-400">{{ $t['date'] }} {{ $t['time'] }} · {{ $t['items'] }} item</div>
                    </div>
                    <span class="shrink-0 text-sm font-semibold text-emerald-300">
                        Rp{{ number_format($t['total'], 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div class="py-6 text-center text-sm text-slate-400">Belum ada transaksi.</div>
            @endforelse
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <h2 class="mb-3 text-sm font-semibold text-white">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('pos.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Buka POS</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Transaksi</span>
                </a>
                <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Laporan</span>
                </a>
                <a href="{{ route('stock-adjustments.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Stok</span>
                </a>
                <a href="{{ route('returns.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Retur</span>
                </a>
                <a href="{{ route('profile.show') }}" class="flex flex-col items-center gap-1 rounded-xl border border-white/10 bg-slate-900/60 p-3 text-center transition hover:border-cyan-400/50">
                    <span class="text-xs text-slate-300">Profil</span>
                </a>
                <a href="{{ route('discounts.index') }}" class="flex flex-col items-center gap-1 rounded-xl border border-amber-400/20 bg-amber-400/10 p-3 text-center transition hover:border-amber-400/50">
                    <span class="text-xs text-amber-300">Diskon</span>
                </a>
            </div>
        </div>
    </section>

    <footer class="mt-8 text-center text-xs text-slate-500">
        ERP POS · {{ \Carbon\Carbon::now()->format('Y') }}
    </footer>
</main>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    try { document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark'; }
    catch (e) { document.documentElement.dataset.theme = 'dark'; }
</script>
<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const clock = document.getElementById('liveClock');
        if (clock) {
            clock.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartData) !!},
                backgroundColor: 'rgba(45, 212, 191, 0.25)',
                borderColor: '#2dd4bf',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    titleColor: '#94a3b8',
                    bodyColor: '#2dd4bf',
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + ctx.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 11 },
                        callback: function(val) {
                            if (val >= 1000000) return 'Rp' + (val / 1000000).toFixed(1) + 'jt';
                            return 'Rp' + val.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
