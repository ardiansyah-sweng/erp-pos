<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Stok Rendah</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
        } catch (error) {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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

        html[data-theme="light"] input {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-amber-500/25 via-cyan-500/15 to-transparent blur-3xl"></div>
    <main class="relative mx-auto max-w-7xl px-4 py-6 lg:px-8">
        <section class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-amber-300/80">Laporan Stok</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Produk stok rendah.</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Pantau produk yang sudah mencapai batas minimum agar pengisian stok bisa dilakukan lebih cepat.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pos.index') }}" class="rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">Kembali POS</a>
                    <a href="{{ route('stock-adjustments.index') }}" class="rounded-full border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm font-medium text-emerald-200 transition hover:bg-emerald-400/20">Penyesuaian Stok</a>
                </div>
            </div>
        </section>

        <section class="mb-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="text-sm text-slate-400">Total Produk Bermasalah</div>
                <div class="mt-2 text-3xl font-semibold text-white">{{ $summary['total'] }}</div>
            </div>
            <div class="rounded-3xl border border-rose-400/20 bg-rose-400/10 p-5 backdrop-blur-xl">
                <div class="text-sm text-rose-200">Stok Habis</div>
                <div class="mt-2 text-3xl font-semibold text-rose-100">{{ $summary['empty'] }}</div>
            </div>
            <div class="rounded-3xl border border-amber-400/20 bg-amber-400/10 p-5 backdrop-blur-xl">
                <div class="text-sm text-amber-200">Stok Menipis</div>
                <div class="mt-2 text-3xl font-semibold text-amber-100">{{ $summary['low'] }}</div>
            </div>
        </section>

        <form method="GET" action="{{ route('stock-reports.low-stock') }}" class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
            <label for="search" class="text-sm text-slate-300">Cari produk stok rendah</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input id="search" name="search" value="{{ $search }}" placeholder="Nama, SKU, atau barcode" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                <button class="rounded-2xl border border-cyan-400/40 bg-cyan-400/15 px-5 py-3 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-400/25">Cari</button>
            </div>
        </form>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">Daftar Produk</h2>
                <span class="text-sm text-slate-400">{{ $products->total() }} produk</span>
            </div>

            @if ($products->isEmpty())
                <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 px-4 py-12 text-center text-sm text-slate-400">
                    Tidak ada produk stok rendah.
                </div>
            @else
                <div class="overflow-hidden rounded-2xl border border-white/10">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-900/80 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3 text-right">Stok</th>
                                <th class="px-4 py-3 text-right">Minimum</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-slate-950/60">
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-white">{{ $product->name }}</div>
                                        <div class="mt-1 text-xs text-slate-400">{{ $product->barcode ?: 'Tanpa barcode' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-200">{{ $product->sku }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $product->stock_status === 'habis' ? 'text-rose-300' : 'text-amber-300' }}">{{ $product->stock_quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-200">{{ $product->min_stock }}</td>
                                    <td class="px-4 py-3">
                                        @if ($product->stock_status === 'habis')
                                            <span class="rounded-full border border-rose-400/30 bg-rose-400/10 px-3 py-1 text-xs font-semibold text-rose-200">Habis</span>
                                        @else
                                            <span class="rounded-full border border-amber-400/30 bg-amber-400/10 px-3 py-1 text-xs font-semibold text-amber-200">Menipis</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-5">
                {{ $products->links() }}
            </div>
        </section>
    </main>
</body>
</html>
