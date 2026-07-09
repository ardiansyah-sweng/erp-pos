<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Cashier - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-violet-300">Manajemen</p>
                <h1 class="mt-2 text-3xl font-bold">Daftar Cashier</h1>
                <p class="mt-2 text-sm text-slate-400">Kelola akun kasir yang terdaftar di sistem.</p>
            </div>
            <a href="{{ route('pos.index') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-violet-400 hover:text-violet-300">
                Kembali ke POS
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Nama</th>
                            <th class="px-5 py-4">Username</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($cashiers as $cashier)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-white">{{ $cashier->name }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $cashier->username }}</td>
                                <td class="px-5 py-4">
                                    <form action="{{ route('cashier.destroy', $cashier->id) }}" method="post" onsubmit="return confirm('Hapus cashier ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-400/40 bg-rose-400/15 px-3 py-1.5 text-sm font-semibold text-rose-300 hover:border-rose-300 hover:bg-rose-400/25 hover:text-white">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-slate-400">Belum ada cashier.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
