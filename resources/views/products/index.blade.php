<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-cyan-300">Inventory</p>
                <h1 class="mt-2 text-3xl font-bold">Manajemen Produk</h1>
                <p class="mt-2 text-sm text-slate-400">Tambah, ubah, dan hapus data produk.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('produk.create') }}" class="rounded-xl bg-cyan-400 px-4 py-2 text-center text-sm font-semibold text-slate-950 hover:bg-cyan-300">
                    + Tambah Produk
                </a>
                <a href="{{ route('pos.index') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-cyan-400 hover:text-cyan-300">
                    Kembali ke POS
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('produk.index') }}" class="mb-6 flex gap-3">
            <input
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama atau SKU..."
                class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
            >
            <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Cari</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-4">SKU / Barcode</th>
                            <th class="px-5 py-4">Nama Produk</th>
                            <th class="px-5 py-4">Harga</th>
                            <th class="px-5 py-4">Stok</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-mono text-sm text-white">{{ $product->sku }}</div>
                                    <div class="text-xs text-slate-500">{{ $product->barcode ?: '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-white">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $product->unit }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="{{ $product->stock_quantity <= $product->min_stock ? 'text-rose-300' : 'text-emerald-300' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    <span class="text-slate-500"> / min {{ $product->min_stock }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-400/15 text-emerald-300' : 'bg-slate-400/15 text-slate-400' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('produk.edit', $product) }}"
                                           class="rounded-lg border border-white/10 px-3 py-1.5 text-xs font-semibold hover:border-cyan-400 hover:text-cyan-300">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('produk.destroy', $product) }}"
                                              onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-rose-400/30 px-3 py-1.5 text-xs font-semibold text-rose-400 hover:bg-rose-400/10">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                    Produk tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>

    </main>
</body>
</html>
