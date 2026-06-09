<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">

    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

    <main class="relative mx-auto max-w-7xl px-4 py-8">

        <!-- Header -->
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">
                Transaction Management
            </p>

            <h1 class="mt-2 text-3xl font-semibold text-white">
                Daftar Transaksi
            </h1>

            <p class="mt-2 text-slate-300">
                Seluruh transaksi yang telah tersimpan dalam sistem ERP POS.
            </p>
        </div>

        <!-- Statistik -->
        <div class="mt-6 grid gap-4 md:grid-cols-3">

            <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-5 py-4">
                <div class="text-sm text-slate-400">
                    Total Transaksi
                </div>

                <div class="mt-2 text-3xl font-bold text-white">
                    {{ $totalTransactions }}
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-5 py-4">
                <div class="text-sm text-slate-400">
                    Total Omzet
                </div>

                <div class="mt-2 text-3xl font-bold text-emerald-300">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-5 py-4">
                <div class="text-sm text-slate-400">
                    Item Terjual
                </div>

                <div class="mt-2 text-3xl font-bold text-cyan-300">
                    {{ $totalItemsSold }}
                </div>
            </div>

        </div>

        <!-- Tabel -->
        <div class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead>
                        <tr class="border-b border-white/10 text-slate-400">
                            <th class="py-3">No Transaksi</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Jumlah Item</th>
                            <th class="py-3">Total</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($transactions as $transaction)

                        <tr class="border-b border-white/5 hover:bg-white/5">

                            <td class="py-4 font-medium text-white">
                                TRX-{{ $transaction->created_at->format('Ymd') }}-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="py-4 text-slate-300">
                                {{ $transaction->created_at->format('d M Y H:i') }}
                            </td>

                            <td class="py-4 text-slate-300">
                                {{ $transaction->details->count() }}
                            </td>

                            <td class="py-4 font-semibold text-emerald-300">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>

                            <td class="py-4 text-center">

                                <a
                                    href="{{ url('/transactions/' . $transaction->id . '/pdf') }}"
                                    target="_blank"
                                    class="inline-flex items-center rounded-xl border border-cyan-400/30 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-300 transition hover:bg-cyan-400/20"
                                >
                                    📄 Lihat PDF
                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Belum ada transaksi yang tersimpan.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>
</html>