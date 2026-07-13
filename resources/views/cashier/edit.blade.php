@extends('layouts.app')

@section('title', 'Edit Kasir')
@section('breadcrumb-prefix', 'Kasir /')
@section('breadcrumb', 'Edit')

@section('content')

<div class="p-8 max-w-xl mx-auto">

    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('cashier.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold">Edit Kasir</h1>
            <p class="mt-1 text-sm text-slate-400">Ubah nama atau username akun kasir.</p>
        </div>
    </div>

    {{-- Alert error --}}
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-300">
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Avatar card --}}
    <div class="mb-6 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4">
        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400 to-teal-500 flex items-center justify-center text-slate-900 font-bold text-xl flex-shrink-0">
            {{ strtoupper(substr($cashier->name, 0, 1)) }}
        </div>
        <div>
            <div class="font-semibold text-white">{{ $cashier->name }}</div>
            <div class="text-sm text-slate-400 font-mono mt-0.5">{{ $cashier->username }}</div>
            <span class="mt-1 inline-block rounded-full border border-cyan-400/30 bg-cyan-400/10 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider text-cyan-300">Kasir</span>
        </div>
    </div>

    {{-- Form --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="mb-5">
            <h2 class="font-semibold text-white">Informasi Akun</h2>
            <p class="mt-1 text-sm text-slate-400">Perubahan username akan langsung berlaku saat login berikutnya.</p>
        </div>

        <form method="POST" action="{{ route('cashier.update', $cashier->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Nama Lengkap
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $cashier->name) }}"
                       placeholder="Nama lengkap"
                       class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Username
                </label>
                <input type="text" name="username"
                       value="{{ old('username', $cashier->username) }}"
                       placeholder="Username login"
                       class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                       required>
                @error('username')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-slate-500">Digunakan untuk login ke sistem.</p>
            </div>

            <div class="pt-2 flex gap-3">
                <a href="{{ route('cashier.index') }}"
                   class="flex-1 rounded-xl border border-white/10 px-4 py-2.5 text-center text-sm font-semibold text-slate-300 hover:border-white/20 hover:text-white transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 hover:bg-cyan-300 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Info: password tidak bisa diubah di sini --}}
    <div class="mt-4 rounded-xl border border-amber-400/20 bg-amber-400/5 px-4 py-3 text-sm text-amber-300/80">
        <span class="font-semibold">Catatan:</span> Untuk mengubah password, kasir bisa masuk ke halaman
        <a href="{{ route('profile.show') }}" class="underline hover:text-amber-200">Profil Saya</a>.
    </div>

</div>

@endsection
