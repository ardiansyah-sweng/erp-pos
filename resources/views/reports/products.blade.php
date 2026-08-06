@extends('layouts.app')

@section('title', 'Laporan Produk')
@section('breadcrumb-prefix', 'Laporan')
@section('breadcrumb', 'Produk')

@section('content')
    @php
        $currency = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
        $periodLabels = [
            'today' => 'Hari ini',
            'yesterday' => 'Kemarin',
            'this_week' => 'Minggu ini',
            'this_month' => 'Bulan ini',
            'custom' => 'Rentang khusus',
        ];
    @endphp

    <section class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-6 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)] sm:p-8">
        <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.25em] text-cyan-400">Analitik lokal</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Laporan Produk</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-400 sm:text-base">
                    Pantau performa penjualan dan kondisi stok dari database POS.
                </p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/30 px-5 py-3 text-sm text-slate-300">
                <span class="block text-xs uppercase tracking-wider text-slate-500">Periode laporan</span>
                <span class="mt-1 block font-semibold text-white">{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</span>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        @foreach ([
            ['label' => 'Total Produk', 'value' => number_format($summary['total_products']), 'color' => 'text-cyan-300'],
            ['label' => 'Produk Aktif', 'value' => number_format($summary['active_products']), 'color' => 'text-emerald-300'],
            ['label' => 'Stok Menipis', 'value' => number_format($summary['low_stock_products']), 'color' => 'text-amber-300'],
            ['label' => 'Stok Habis', 'value' => number_format($summary['out_of_stock_products']), 'color' => 'text-rose-300'],
            ['label' => 'Barang Terjual', 'value' => number_format($summary['quantity_sold']), 'color' => 'text-violet-300'],
            ['label' => 'Pendapatan', 'value' => $currency($summary['revenue']), 'color' => 'text-sky-300'],
        ] as $card)
            <article class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 truncate text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-6 rounded-2xl border border-white/10 bg-[#0d1b2a] p-5">
        <form method="GET" action="{{ route('reports.products') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-2">
                <label for="filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Periode</label>
                <select id="filter" name="filter" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">
                    @foreach ($periodLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="custom-date lg:col-span-2 {{ $filter === 'custom' ? '' : 'hidden' }}">
                <label for="start_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Mulai</label>
                <input id="start_date" name="start_date" type="date" value="{{ $startDate }}" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">
            </div>

            <div class="custom-date lg:col-span-2 {{ $filter === 'custom' ? '' : 'hidden' }}">
                <label for="end_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Selesai</label>
                <input id="end_date" name="end_date" type="date" value="{{ $endDate }}" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">
            </div>

            <div class="lg:col-span-2">
                <label for="category_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Kategori</label>
                <select id="category_id" name="category_id" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="stock_status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Status stok</label>
                <select id="stock_status" name="stock_status" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">
                    <option value="">Semua status</option>
                    <option value="aman" @selected($stockStatus === 'aman')>Aman</option>
                    <option value="menipis" @selected($stockStatus === 'menipis')>Menipis</option>
                    <option value="habis" @selected($stockStatus === 'habis')>Habis</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Cari produk</label>
                <input id="search" name="search" type="search" value="{{ $search }}" placeholder="Nama / SKU / barcode" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none placeholder:text-slate-600 focus:border-cyan-400">
            </div>

            <div class="flex gap-2 lg:col-span-12 lg:justify-end">
                <a href="{{ route('reports.products.pdf', request()->query()) }}" class="rounded-xl border border-emerald-400/40 px-4 py-2.5 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-400/10">Export PDF</a>
                <a href="{{ route('reports.products') }}" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/5">Reset</a>
                <button type="submit" class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">Terapkan Filter</button>
            </div>
        </form>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-[#0d1b2a] xl:col-span-2">
            <div class="border-b border-white/10 px-5 py-4">
                <h2 class="font-bold text-white">Performa Produk</h2>
                <p class="mt-1 text-xs text-slate-500">Diurutkan berdasarkan jumlah barang terjual.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] text-left text-sm text-slate-300">
                    <thead class="bg-[#091523] text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Produk</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4 text-right">Stok</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Terjual</th>
                            <th class="px-5 py-4 text-right">Transaksi</th>
                            <th class="px-5 py-4 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($products as $product)
                            @php
                                $status = $product->stock_status;
                                $statusClass = match ($status) {
                                    'habis' => 'bg-rose-400/10 text-rose-300 border-rose-400/20',
                                    'menipis' => 'bg-amber-400/10 text-amber-300 border-amber-400/20',
                                    default => 'bg-emerald-400/10 text-emerald-300 border-emerald-400/20',
                                };
                            @endphp
                            <tr class="transition hover:bg-white/[.03]">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-white">{{ $product->name }}</p>
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $product->sku }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-400">{{ $product->category_name ?? 'Tanpa kategori' }}</td>
                                <td class="px-5 py-4 text-right font-semibold text-white">{{ number_format($product->stock_quantity) }} {{ $product->unit }}</td>
                                <td class="px-5 py-4"><span class="rounded-full border px-2.5 py-1 text-xs font-semibold capitalize {{ $statusClass }}">{{ $status }}</span></td>
                                <td class="px-5 py-4 text-right font-semibold text-violet-300">{{ number_format($product->quantity_sold) }}</td>
                                <td class="px-5 py-4 text-right">{{ number_format($product->transaction_count) }}</td>
                                <td class="px-5 py-4 text-right font-semibold text-cyan-300">{{ $currency($product->revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-14 text-center text-slate-500">Tidak ada produk yang cocok dengan filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="border-t border-white/10 px-5 py-4">{{ $products->links() }}</div>
            @endif
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5">
                <h2 class="font-bold text-white">Produk Terlaris</h2>
                <p class="mt-1 text-xs text-slate-500">Berdasarkan periode yang dipilih.</p>
                <div class="mt-4 space-y-3">
                    @forelse ($topProducts as $product)
                        <div class="flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-slate-950/30 p-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-white">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} · {{ number_format($product->quantity_sold) }} terjual</p>
                            </div>
                            <span class="shrink-0 text-xs font-semibold text-cyan-300">{{ $currency($product->revenue) }}</span>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-white/10 p-5 text-center text-sm text-slate-500">Belum ada penjualan.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-white">Perlu Restock</h2>
                        <p class="mt-1 text-xs text-slate-500">Stok sudah menyentuh batas minimum.</p>
                    </div>
                    <a href="{{ route('stock-adjustments.index') }}" class="text-xs font-semibold text-cyan-300 hover:underline">Atur stok</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-slate-950/30 p-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-white">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">Minimum {{ number_format($product->min_stock) }} {{ $product->unit }}</p>
                            </div>
                            <span class="shrink-0 rounded-lg bg-rose-400/10 px-2.5 py-1 text-xs font-bold text-rose-300">{{ number_format($product->stock_quantity) }}</span>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-white/10 p-5 text-center text-sm text-slate-500">Semua stok aman.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filter = document.getElementById('filter');
            const customDates = document.querySelectorAll('.custom-date');
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            filter.addEventListener('change', () => {
                const isCustom = filter.value === 'custom';
                customDates.forEach((element) => element.classList.toggle('hidden', !isCustom));
                startDate.required = isCustom;
                endDate.required = isCustom;
            });
        });
    </script>
@endpush
