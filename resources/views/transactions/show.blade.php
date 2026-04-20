<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #{{ $transaction->id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Breadcrumb -->
        <div class="bg-white px-6 py-4 mb-6 rounded-lg shadow-sm text-sm">
            <a href="{{ route('transaction.index') }}" class="text-blue-600 hover:underline">📊 Daftar Transaksi</a>
            <span class="text-gray-500 mx-2">/</span>
            <span class="text-gray-700">Detail Transaksi</span>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Transaksi #{{ $transaction->id }}</h1>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('transaction.struk-preview', $transaction->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-semibold text-sm transition" target="_blank">
                        🔍 Preview
                    </a>
                    <a href="{{ route('transaction.print-pdf', $transaction->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-semibold text-sm transition">
                        📥 Download PDF
                    </a>
                    <a href="{{ route('transaction.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded font-semibold text-sm transition">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Informasi Transaksi -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-300 font-semibold text-gray-800">
                Informasi Transaksi
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="border border-gray-300 p-4 rounded bg-gray-50">
                        <div class="text-xs font-bold text-gray-600 uppercase mb-1">ID Transaksi</div>
                        <div class="text-2xl font-bold text-gray-800">#{{ $transaction->id }}</div>
                    </div>
                    <div class="border border-gray-300 p-4 rounded bg-gray-50">
                        <div class="text-xs font-bold text-gray-600 uppercase mb-1">Tanggal</div>
                        <div class="text-xl font-bold text-gray-800">{{ $transaction->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="border border-gray-300 p-4 rounded bg-gray-50">
                        <div class="text-xs font-bold text-gray-600 uppercase mb-1">Waktu</div>
                        <div class="text-xl font-bold text-gray-800">{{ $transaction->created_at->format('H:i:s') }}</div>
                    </div>
                    <div class="border border-gray-300 p-4 rounded bg-gray-50">
                        <div class="text-xs font-bold text-gray-600 uppercase mb-1">Total Item</div>
                        <div class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm font-semibold">
                            {{ $transaction->details->sum('quantity') }} item(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-300 font-semibold text-gray-800">
                Detail Barang
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-800 text-sm">No</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-800 text-sm">Produk</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-800 text-sm">Qty</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-800 text-sm">Harga Satuan</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-800 text-sm">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaction->details as $index => $detail)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $detail->product_id }}</td>
                                <td class="px-4 py-3 text-right">{{ $detail->quantity }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <strong class="text-green-600">Rp {{ number_format($detail->amount, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">Tidak ada item</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 p-4 rounded mt-6">
                    <div class="flex justify-between mb-3 text-sm">
                        <span class="text-gray-700">Jumlah Item:</span>
                        <span class="font-semibold text-gray-800">{{ $transaction->details->sum('quantity') }} item(s)</span>
                    </div>
                    <div class="flex justify-between mb-3 text-sm">
                        <span class="text-gray-700">Total Harga:</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($transaction->details->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t-2 border-gray-300 text-lg font-bold">
                        <span class="text-gray-800">TOTAL TRANSAKSI:</span>
                        <span class="text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center py-8 text-gray-500 text-sm">
            <p>Terima kasih telah berbelanja</p>
        </div>
    </div>
</body>
</html>
