@extends('layouts.app')

@section('title', 'Laporan Pembayaran')
@section('breadcrumb-prefix', 'Laporan')
@section('breadcrumb', 'Metode Pembayaran')

@section('content')
    @php
        $currency = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
        $periodLabels = ['today' => 'Hari ini', 'yesterday' => 'Kemarin', 'this_week' => 'Minggu ini', 'this_month' => 'Bulan ini', 'custom' => 'Rentang khusus'];
        $methodLabels = ['cash' => 'Tunai', 'card' => 'Kartu', 'e_wallet' => 'E-Wallet', 'bank_transfer' => 'Transfer Bank', 'qris' => 'QRIS'];
    @endphp

    <section class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-6 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)] sm:p-8">
        <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div><p class="text-sm font-semibold uppercase tracking-[.25em] text-cyan-400">Analitik lokal</p><h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Laporan Pembayaran</h1><p class="mt-2 max-w-2xl text-sm text-slate-400 sm:text-base">Pantau nilai transaksi berhasil berdasarkan metode pembayaran.</p></div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/30 px-5 py-3 text-sm text-slate-300"><span class="block text-xs uppercase tracking-wider text-slate-500">Periode laporan</span><span class="mt-1 block font-semibold text-white">{{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</span></div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([['label' => 'Total Pembayaran', 'value' => number_format($summary->total_payments), 'color' => 'text-cyan-300'], ['label' => 'Nilai Pembayaran', 'value' => $currency($summary->total_amount), 'color' => 'text-emerald-300'], ['label' => 'Rata-rata Transaksi', 'value' => $currency($summary->average_amount), 'color' => 'text-violet-300']] as $card)
            <article class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $card['label'] }}</p><p class="mt-3 truncate text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p></article>
        @endforeach
    </section>

    <section class="mt-6 rounded-2xl border border-white/10 bg-[#0d1b2a] p-5">
        <form method="GET" action="{{ route('reports.payments') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-3"><label for="filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Periode</label><select id="filter" name="filter" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400">@foreach ($periodLabels as $value => $label)<option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="custom-date lg:col-span-3 {{ $filter === 'custom' ? '' : 'hidden' }}"><label for="start_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Mulai</label><input id="start_date" name="start_date" type="date" value="{{ $startDate }}" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400"></div>
            <div class="custom-date lg:col-span-3 {{ $filter === 'custom' ? '' : 'hidden' }}"><label for="end_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Selesai</label><input id="end_date" name="end_date" type="date" value="{{ $endDate }}" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white outline-none focus:border-cyan-400"></div>
            <div class="flex gap-2 lg:col-span-3 lg:justify-end"><a href="{{ route('reports.payments') }}" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/5">Reset</a><button type="submit" class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">Terapkan</button></div>
        </form>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-[#0d1b2a] xl:col-span-2">
            <div class="border-b border-white/10 px-5 py-4"><h2 class="font-bold text-white">Pembayaran Terbaru</h2><p class="mt-1 text-xs text-slate-500">Hanya pembayaran berstatus berhasil yang ditampilkan.</p></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[680px] text-left text-sm text-slate-300"><thead class="bg-[#091523] text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-4">Transaksi</th><th class="px-5 py-4">Metode</th><th class="px-5 py-4">Referensi</th><th class="px-5 py-4 text-right">Nilai</th></tr></thead><tbody class="divide-y divide-white/5">
                @forelse ($recentPayments as $payment)
                    <tr class="transition hover:bg-white/[.03]"><td class="px-5 py-4"><p class="font-semibold text-white">#{{ $payment->transaction_id }}</p><p class="mt-1 text-xs text-slate-500">{{ \Carbon\Carbon::parse($payment->transaction_date)->format('d M Y H:i') }}</p></td><td class="px-5 py-4"><span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-2.5 py-1 text-xs font-semibold text-cyan-300">{{ $methodLabels[$payment->payment_method] ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td><td class="px-5 py-4 font-mono text-xs text-slate-400">{{ $payment->reference_number ?: '-' }}</td><td class="px-5 py-4 text-right font-semibold text-emerald-300">{{ $currency($payment->amount) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-14 text-center text-slate-500">Belum ada pembayaran berhasil pada periode ini.</td></tr>
                @endforelse
            </tbody></table></div>
            @if ($recentPayments->hasPages())<div class="border-t border-white/10 px-5 py-4">{{ $recentPayments->links() }}</div>@endif
        </section>
        <aside class="space-y-6">
            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5"><h2 class="font-bold text-white">Ringkasan Metode</h2><div class="mt-4 space-y-3">@forelse ($methodSummary as $method)<div class="rounded-xl border border-white/5 bg-slate-950/30 p-3"><div class="flex items-center justify-between gap-3"><p class="font-semibold text-white">{{ $methodLabels[$method->payment_method] ?? ucfirst(str_replace('_', ' ', $method->payment_method)) }}</p><span class="text-xs font-semibold text-cyan-300">{{ $currency($method->total_amount) }}</span></div><p class="mt-1 text-xs text-slate-500">{{ number_format($method->total_payments) }} pembayaran</p></div>@empty<p class="rounded-xl border border-dashed border-white/10 p-5 text-center text-sm text-slate-500">Belum ada data pembayaran.</p>@endforelse</div></section>
            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5"><h2 class="font-bold text-white">Total Harian</h2><div class="mt-4 space-y-3">@forelse ($dailyPayments as $payment)<div class="flex items-center justify-between gap-3 border-b border-white/5 pb-3 last:border-0 last:pb-0"><span class="text-sm text-slate-400">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span><span class="text-sm font-semibold text-emerald-300">{{ $currency($payment->total_amount) }}</span></div>@empty<p class="text-center text-sm text-slate-500">Belum ada total harian.</p>@endforelse</div></section>
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
