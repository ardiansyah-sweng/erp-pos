<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto p-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">📊 Daftar Transaksi</h1>
            <p class="text-gray-600 text-sm">Kelola dan lihat semua transaksi penjualan</p>
        </div>

        @if($transactions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold mb-1">{{ $transactions->total() }}</div>
                    <div class="text-sm opacity-90">Total Transaksi</div>
                </div>
                <div class="bg-gradient-to-br from-pink-500 to-red-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold mb-1">Rp {{ number_format($transactions->sum('total'), 0, ',', '.') }}</div>
                    <div class="text-sm opacity-90">Total Penjualan</div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold mb-1">{{ $transactions->flatMap(fn($t) => $t->details)->count() }}</div>
                    <div class="text-sm opacity-90">Total Item</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 text-sm w-1/12">ID</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 text-sm w-3/12">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 text-sm w-2/12">Jumlah Item</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 text-sm w-2/12">Total</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-800 text-sm w-4/12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-4 py-3"><strong>#{{ $transaction->id }}</strong></td>
                                <td class="px-4 py-3">
                                    <div>{{ $transaction->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                        {{ $transaction->details->sum('quantity') }} item(s)
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('transaction.show', $transaction->id) }}" class="inline-block bg-cyan-500 hover:bg-cyan-600 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                            👁 Lihat
                                        </a>
                                        <a href="{{ route('transaction.struk-preview', $transaction->id) }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold transition" target="_blank">
                                            🔍 Preview
                                        </a>
                                        <a href="{{ route('transaction.print-pdf', $transaction->id) }}" class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                            📥 PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($transactions->hasPages())
            <div class="flex justify-center gap-1 mt-8">
                {{ $transactions->links() }}
            </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow-md p-16 text-center">
                <p class="text-gray-500 text-lg mb-4">📭 Belum ada data transaksi</p>
                <p class="text-gray-400 text-xs">Jalankan seeder untuk membuat data contoh</p>
            </div>
        @endif
    </div>
</body>
</html>
