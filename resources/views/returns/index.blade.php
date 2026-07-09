@extends('layouts.app')

@section('title', 'Retur Transaksi')

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

    html[data-theme="light"] input,
    html[data-theme="light"] textarea {
        background-color: #ffffff !important;
        color: #0f172a !important;
    }
</style>
@endpush

@section('content')

<div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-rose-500/20 via-cyan-500/20 to-transparent blur-3xl"></div>
<main class="relative mx-auto max-w-7xl px-4 py-6 lg:px-8">
    <section class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Retur Transaksi</p>
                <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Pengembalian barang transaksi.</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-300">Pilih transaksi, tentukan jumlah item yang diretur, lalu stok barang akan otomatis dikembalikan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('pos.index') }}" class="rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">Kembali POS</a>
                <a href="{{ route('transactions.index') }}" class="rounded-full border border-cyan-400/40 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-200 transition hover:bg-cyan-400/20">Riwayat Transaksi</a>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-medium text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">
        <div class="space-y-5">
            <form method="GET" action="{{ route('returns.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <label for="search" class="text-sm text-slate-300">Cari ID transaksi</label>
                <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                    <input id="search" name="search" value="{{ $search }}" placeholder="Contoh: 12 atau TRX-0012" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                    <button class="rounded-2xl border border-cyan-400/40 bg-cyan-400/15 px-5 py-3 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-400/25">Cari</button>
                </div>
            </form>

            @forelse ($transactions as $transaction)
                <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="flex flex-col gap-3 border-b border-white/10 pb-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div class="text-lg font-semibold text-white">TRX-{{ str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="mt-1 text-sm text-slate-400">{{ $transaction->created_at?->translatedFormat('d F Y H:i') }}</div>
                        </div>
                        <div class="text-left md:text-right">
                            <div class="text-sm text-slate-400">Total transaksi</div>
                            <div class="text-xl font-semibold text-emerald-300">Rp{{ number_format($transaction->total, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('returns.store', $transaction) }}" class="mt-4 space-y-4">
                        @csrf
                        <div class="overflow-hidden rounded-2xl border border-white/10">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-900/80 text-slate-400">
                                    <tr>
                                        <th class="px-4 py-3">Produk</th>
                                        <th class="px-4 py-3 text-right">Terjual</th>
                                        <th class="px-4 py-3 text-right">Sudah Retur</th>
                                        <th class="px-4 py-3 text-right">Sisa Barang</th>
                                        <th class="px-4 py-3 text-right">Qty Retur</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10 bg-slate-950/60">
                                    @foreach ($transaction->details as $detail)
                                        @php
                                            $returned = $detail->returnDetails->sum('quantity');
                                            $available = max(0, $detail->quantity - $returned);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-white">{{ $detail->product?->name ?? 'Produk #' . $detail->product_id }}</div>
                                                <div class="mt-1 text-xs text-slate-400">Rp{{ number_format($detail->price, 0, ',', '.') }} / item</div>
                                            </td>
                                            <td class="px-4 py-3 text-right text-slate-200">{{ $detail->quantity }}</td>
                                            <td class="px-4 py-3 text-right text-rose-300">{{ $returned }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-emerald-300">{{ $available }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <input name="items[{{ $detail->id }}]" type="number" min="0" max="{{ $available }}" value="0" {{ $available === 0 ? 'disabled' : '' }} class="w-24 rounded-xl border border-white/10 bg-slate-950/70 px-3 py-2 text-right text-white outline-none focus:border-cyan-400 disabled:opacity-40">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-slate-400">Kolom Terjual tetap mengikuti transaksi asli. Barang yang sudah dikembalikan terlihat di kolom Sudah Retur, sedangkan Sisa Barang adalah jumlah yang masih bisa diretur.</p>
                        <div>
                            <label class="text-sm text-slate-300" for="reason-{{ $transaction->id }}">Alasan retur</label>
                            <textarea id="reason-{{ $transaction->id }}" name="reason" rows="2" maxlength="255" placeholder="Contoh: barang rusak / salah input" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button class="rounded-2xl bg-gradient-to-r from-rose-400 to-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">Simpan Retur</button>
                        </div>
                    </form>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/5 px-4 py-12 text-center text-sm text-slate-400">
                    Belum ada transaksi yang bisa ditampilkan.
                </div>
            @endforelse

            {{ $transactions->links() }}
        </div>

        <aside class="space-y-5">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <h2 class="text-lg font-semibold text-white">Riwayat Retur</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($returns as $return)
                        <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-white">{{ $return->return_code }}</div>
                                    <div class="mt-1 text-xs text-slate-400">TRX-{{ str_pad((string) $return->transaction_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                                <div class="text-right font-semibold text-emerald-300">Rp{{ number_format($return->total_refund, 0, ',', '.') }}</div>
                            </div>
                            <div class="mt-3 text-xs text-slate-400">{{ $return->details->sum('quantity') }} item diretur</div>
                            @if ($return->reason)
                                <div class="mt-2 rounded-xl bg-white/5 px-3 py-2 text-xs text-slate-300">{{ $return->reason }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 px-4 py-8 text-center text-sm text-slate-400">
                            Belum ada retur tersimpan.
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>
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
