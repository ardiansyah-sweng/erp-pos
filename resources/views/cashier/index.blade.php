<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>
    <main class="relative mx-auto min-h-screen max-w-7xl px-4 py-6 lg:px-8">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Manajemen Kasir</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Daftar seluruh kasir tersedia</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Lihat nama, username, dan tanggal pembuatan akun kasir pada sistem.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-5 py-4 text-sm text-slate-300">
                    Total kasir
                    <div class="mt-2 text-3xl font-semibold text-white">{{ $cashiers->count() }}</div>
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">Daftar Kasir</h2>
                    <p class="text-sm text-slate-400">Semua kasir yang terdaftar di sistem.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-2 text-sm text-slate-300">
                    Diperbarui {{ now()->format('d M Y') }}
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-slate-950/50">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-200">
                    <thead class="bg-slate-900/90 text-slate-400">
                        <tr>
                            <th class="px-4 py-4">ID</th>
                            <th class="px-4 py-4">Nama</th>
                            <th class="px-4 py-4">Username</th>
                            <th class="px-4 py-4">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-slate-950/60">
                        @forelse ($cashiers as $cashier)
                            <tr class="hover:bg-slate-900/80">
                                <td class="whitespace-nowrap px-4 py-4 font-medium text-white">{{ $cashier->id }}</td>
                                <td class="px-4 py-4">{{ $cashier->name }}</td>
                                <td class="px-4 py-4">{{ $cashier->username }}</td>
                                <td class="px-4 py-4 text-slate-400">{{ $cashier->created_at ? $cashier->created_at->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-400">Belum ada kasir terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
