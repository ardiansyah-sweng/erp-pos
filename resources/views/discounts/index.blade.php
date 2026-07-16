@extends('layouts.app')

@section('title', 'Manajemen Diskon')
@section('breadcrumb-prefix', 'Master Data')
@section('breadcrumb', 'Diskon')

@push('styles')
<style>
    html[data-theme="light"] .card,
    html[data-theme="light"] .bg-\\[\\#0d1424\\] {
        background: #ffffff !important;
        border-color: #dbe3ea !important;
    }
    html[data-theme="light"] .text-slate-400 { color: #64748b !important; }
    html[data-theme="light"] .text-white     { color: #0f172a !important; }
    html[data-theme="light"] table thead     { background: #f1f5f9 !important; }
    html[data-theme="light"] tbody tr        { border-color: #e2e8f0 !important; }
</style>
@endpush

@section('content')
<div class="p-6 lg:p-8 space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Promo &amp; Diskon</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola diskon persentase atau nominal untuk produk tertentu.</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-medium text-emerald-300">
        <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="flex items-center gap-3 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-300">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">

        {{-- ============ KIRI: Daftar Diskon ============ --}}
        <div class="space-y-4">

            {{-- Search --}}
            <form method="GET" action="{{ route('discounts.index') }}" class="card p-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="input-icon w-4 h-4"></i>
                        <input
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama diskon, produk, atau SKU…"
                            class="input pl-10"
                        >
                    </div>
                    <button class="flex items-center gap-2 rounded-2xl bg-cyan-500 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-cyan-400 transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Cari
                    </button>
                    @if($search)
                    <a href="{{ route('discounts.index') }}" class="flex items-center gap-2 rounded-2xl border border-white/10 px-5 py-3 text-sm text-slate-400 hover:text-white transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Reset
                    </a>
                    @endif
                </div>
            </form>

            {{-- Tabel --}}
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-white/[0.03] text-slate-400 text-xs uppercase tracking-wider">
                                <th class="px-5 py-3.5 text-left font-medium">Nama Diskon</th>
                                <th class="px-5 py-3.5 text-left font-medium">Produk</th>
                                <th class="px-5 py-3.5 text-right font-medium">Nilai</th>
                                <th class="px-5 py-3.5 text-left font-medium">Periode</th>
                                <th class="px-5 py-3.5 text-center font-medium">Status</th>
                                <th class="px-5 py-3.5 text-center font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($discounts as $discount)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="px-5 py-4 font-medium text-white">
                                    {{ $discount->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-white">{{ $discount->product?->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $discount->product?->sku }}</div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($discount->type === 'percentage')
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-400/30 bg-amber-400/10 px-2.5 py-1 text-xs font-semibold text-amber-300">
                                            <i data-lucide="percent" class="w-3 h-3"></i>
                                            {{ $discount->value }}%
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-2.5 py-1 text-xs font-semibold text-emerald-300">
                                            Rp{{ number_format($discount->value, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-400">
                                    <div>{{ $discount->start_date->format('d M Y') }}</div>
                                    <div class="text-slate-500">s/d {{ $discount->end_date->format('d M Y') }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($discount->isCurrentlyActive())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-700/50 px-3 py-1 text-xs font-semibold text-slate-500">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <form method="POST" action="{{ route('discounts.destroy', $discount) }}" onsubmit="return confirm('Hapus diskon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1.5 rounded-xl border border-rose-400/30 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-300 transition hover:bg-rose-400/20">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-slate-500">
                                        <i data-lucide="tag" class="w-10 h-10 opacity-30"></i>
                                        <p class="text-sm">Belum ada diskon. Tambahkan diskon pertama di panel kanan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($discounts->hasPages())
                <div class="px-5 py-4 border-t border-white/5">
                    {{ $discounts->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- ============ KANAN: Form Tambah ============ --}}
        <aside>
            <div class="card p-5 sticky top-24">
                <div class="flex items-center gap-3 mb-5">
                    <div class="rounded-xl bg-amber-400/15 p-2">
                        <i data-lucide="tag" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    <h2 class="text-base font-semibold text-white">Tambah Diskon Baru</h2>
                </div>

                <form method="POST" action="{{ route('discounts.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Produk</label>
                        <select name="product_id" class="input" style="padding-left:16px">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Nama Diskon</label>
                        <input name="name" value="{{ old('name') }}" placeholder="Contoh: Promo Lebaran" class="input" style="padding-left:16px">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Tipe Diskon</label>
                        <select name="type" class="input" style="padding-left:16px">
                            <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed"      {{ old('type') === 'fixed'      ? 'selected' : '' }}>Nominal (Rp)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Nilai Diskon</label>
                        <input name="value" type="number" min="1" value="{{ old('value') }}" placeholder="Contoh: 10 untuk 10%" class="input" style="padding-left:16px">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Tanggal Mulai</label>
                            <input name="start_date" type="date" value="{{ old('start_date') }}" class="input" style="padding-left:16px">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Tanggal Selesai</label>
                            <input name="end_date" type="date" value="{{ old('end_date') }}" class="input" style="padding-left:16px">
                        </div>
                    </div>

                    <label for="is_active" class="flex cursor-pointer items-start gap-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:border-white/20 has-[:checked]:border-emerald-400/40 has-[:checked]:bg-emerald-400/[0.06]">
                        <div class="relative mt-0.5 shrink-0">
                            <input name="is_active" type="checkbox" value="1" checked id="is_active" class="peer sr-only">
                            <div class="h-6 w-11 rounded-full border border-white/20 bg-slate-700 transition peer-checked:border-emerald-400/60 peer-checked:bg-emerald-500"></div>
                            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-white">Aktifkan Diskon</div>
                            <div class="mt-0.5 text-xs text-slate-400 leading-relaxed">Jika diaktifkan, diskon akan otomatis diterapkan ke POS saat tanggal masuk periode berlaku. Nonaktifkan untuk menjeda diskon tanpa menghapus data.</div>
                        </div>
                    </label>

                    <button class="w-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-amber-400 to-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Diskon
                    </button>
                </form>
            </div>
        </aside>

    </div>
</div>
@endsection