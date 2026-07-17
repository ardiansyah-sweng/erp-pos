@extends('layouts.app')

@section('title', 'Riwayat Sinkronisasi')
@section('breadcrumb-prefix', 'Laporan')
@section('breadcrumb', 'Riwayat Sinkronisasi')

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
            <h1 class="text-2xl font-bold text-white">Riwayat Sinkronisasi</h1>
            <p class="mt-1 text-sm text-slate-400">Monitoring proses sinkronisasi data ERP POS.</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2">
        <div class="card p-6">
            <h4 class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Sinkronisasi</h4>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $summary['total'] }}</h2>
        </div>
        <div class="card p-6">
            <h4 class="text-xs font-medium text-slate-400 uppercase tracking-wider">Berhasil</h4>
            <h2 class="text-3xl font-bold text-white mt-2">{{ $summary['success'] }}</h2>
        </div>
    </div>

    {{-- Search Box --}}
    <form method="GET" class="card p-4">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i data-lucide="search" class="input-icon w-4 h-4"></i>
                <input
                    type="text"
                    name="search"
                    placeholder="Cari module..."
                    value="{{ $search }}"
                    class="input pl-10"
                >
            </div>
            <button class="flex items-center gap-2 rounded-2xl bg-cyan-500 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-cyan-400 transition">
                <i data-lucide="search" class="w-4 h-4"></i>
                Cari
            </button>
        </div>
    </form>

    {{-- Table Data --}}
    @if($histories->count())
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-white/[0.03] text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-5 py-3.5 text-left font-medium" style="width:60px;">No</th>
                        <th class="px-5 py-3.5 text-left font-medium">Module</th>
                        <th class="px-5 py-3.5 text-left font-medium">Status</th>
                        <th class="px-5 py-3.5 text-left font-medium">Pesan</th>
                        <th class="px-5 py-3.5 text-left font-medium">Waktu Sinkronisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($histories as $history)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="px-5 py-4 text-slate-400">
                            {{ $histories->firstItem() + $loop->index }}
                        </td>
                        <td class="px-5 py-4 font-medium text-white">
                            {{ $history->module }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                {{ $history->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-300">
                            {{ $history->message ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-slate-400">
                            {{ \Carbon\Carbon::parse($history->synced_at)->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($histories->hasPages())
        <div class="px-5 py-4 border-t border-white/5 flex items-center justify-between bg-white/[0.01]">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($histories->onFirstPage())
                    <span class="text-xs text-slate-500 cursor-not-allowed">Sebelumnya</span>
                @else
                    <a href="{{ $histories->previousPageUrl() }}" class="text-xs text-cyan-400">Sebelumnya</a>
                @endif
                @if($histories->hasMorePages())
                    <a href="{{ $histories->nextPageUrl() }}" class="text-xs text-cyan-400">Selanjutnya</a>
                @else
                    <span class="text-xs text-slate-500 cursor-not-allowed">Selanjutnya</span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-400">
                        Halaman <span class="font-semibold text-white">{{ $histories->currentPage() }}</span> dari <span class="font-semibold text-white">{{ $histories->lastPage() }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    @if($histories->onFirstPage())
                        <span class="px-3 py-1.5 text-xs border border-white/10 rounded-xl text-slate-500 cursor-not-allowed">← Sebelumnya</span>
                    @else
                        <a href="{{ $histories->previousPageUrl() }}" class="px-3 py-1.5 text-xs border border-white/10 rounded-xl text-slate-300 hover:text-white transition">← Sebelumnya</a>
                    @endif

                    @if($histories->hasMorePages())
                        <a href="{{ $histories->nextPageUrl() }}" class="px-3 py-1.5 text-xs border border-white/10 rounded-xl text-slate-300 hover:text-white transition">Selanjutnya →</a>
                    @else
                        <span class="px-3 py-1.5 text-xs border border-white/10 rounded-xl text-slate-500 cursor-not-allowed">Selanjutnya →</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="card p-16 text-center">
        <div class="flex flex-col items-center gap-3 text-slate-500">
            <i data-lucide="refresh-cw" class="w-10 h-10 opacity-30 animate-spin-slow"></i>
            <p class="text-sm">Belum ada riwayat sinkronisasi.</p>
        </div>
    </div>
    @endif

</div>
@endsection