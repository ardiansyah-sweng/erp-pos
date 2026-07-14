@extends('layouts.app')

@section('title', 'Manajemen User')
@section('breadcrumb-prefix', 'Pengaturan')
@section('breadcrumb', 'Manajemen User')

@section('content')

<div class="p-8">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Manajemen User</h1>
            <p class="mt-2 text-sm text-slate-400">Kelola akun kasir & admin yang dapat masuk ke sistem.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-200">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        <!-- Form tambah user -->
        <div class="card p-6 lg:col-span-1">
            <h2 class="mb-4 text-lg font-semibold">Tambah User</h2>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-white/10 bg-[#020617] px-4 py-2.5 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-white/10 bg-[#020617] px-4 py-2.5 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Password</label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full rounded-xl border border-white/10 bg-[#020617] px-4 py-2.5 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Peran</label>
                    <select name="role" required
                        class="w-full rounded-xl border border-white/10 bg-[#020617] px-4 py-2.5 outline-none focus:border-cyan-400">
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full rounded-xl bg-cyan-500 px-4 py-2.5 font-semibold text-[#04121a] hover:bg-cyan-400">
                    Simpan
                </button>
            </form>
        </div>

        <!-- Daftar user -->
        <div class="card overflow-hidden lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-white/10 text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-medium">Nama</th>
                            <th class="px-6 py-4 font-medium">Email</th>
                            <th class="px-6 py-4 font-medium">Peran</th>
                            <th class="px-6 py-4 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-slate-300">{{ $user->username }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->role === 'admin')
                                        <span class="rounded-full bg-cyan-500/15 px-3 py-1 text-xs font-semibold text-cyan-300">Admin</span>
                                    @else
                                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300">Kasir</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($user->id !== session('cashier_id'))
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                            onsubmit="return confirm('Hapus pengguna {{ $user->username }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-300 hover:text-rose-200">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-500">(Anda)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
