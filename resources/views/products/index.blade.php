<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Produk</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">

<div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

<main class="relative mx-auto max-w-7xl px-4 py-8 lg:px-8">

    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">
                    Product Management
                </p>

                <h1 class="mt-2 text-3xl font-semibold text-white">
                    Daftar Produk
                </h1>

                <p class="mt-2 text-slate-300">
                    Seluruh produk yang tersedia di sistem ERP POS.
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                <div class="text-slate-400 text-sm">
                    Total Produk
                </div>

                <div class="text-xl font-semibold text-white">
                    {{ $products->count() }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400">
                        <th class="py-3">SKU</th>
                        <th class="py-3">Barcode</th>
                        <th class="py-3">Nama Produk</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3">Harga</th>
                        <th class="py-3">Stok</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3">{{ $product->sku }}</td>
                        <td class="py-3">{{ $product->barcode }}</td>
                        <td class="py-3 font-medium text-white">
                            {{ $product->name }}
                        </td>
                        <td class="py-3">{{ $product->unit }}</td>
                        <td class="py-3 text-emerald-300">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3">
                            {{ $product->stock_quantity }}
                        </td>
                        <td class="py-3">
                            @if($product->is_active)
                                <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-300">
                                    Aktif
                                </span>
                            @else
                                <span class="rounded-full bg-rose-500/20 px-3 py-1 text-xs text-rose-300">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">
                            Tidak ada data produk.
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