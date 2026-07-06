<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product ? 'Edit' : 'Tambah' }} Produk - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-2xl px-4 py-8 sm:px-6">

        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-cyan-300">Inventory</p>
            <h1 class="mt-2 text-3xl font-bold">{{ $product ? 'Edit Produk' : 'Tambah Produk' }}</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $product ? route('produk.update', $product) : route('produk.store') }}"
              class="space-y-5 rounded-2xl border border-white/10 bg-white/5 p-6">
            @csrf
            @if ($product)
                @method('PUT')
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-slate-400">SKU <span class="text-rose-400">*</span></label>
                    <input name="sku" value="{{ old('sku', $product?->sku) }}" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Barcode</label>
                    <input name="barcode" value="{{ old('barcode', $product?->barcode) }}"
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-slate-400">Nama Produk <span class="text-rose-400">*</span></label>
                <input name="name" value="{{ old('name', $product?->name) }}" required
                       class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
            </div>

            <div>
                <label class="mb-1 block text-sm text-slate-400">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">{{ old('description', $product?->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Satuan <span class="text-rose-400">*</span></label>
                    <input name="unit" value="{{ old('unit', $product?->unit ?? 'pcs') }}" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Harga Jual <span class="text-rose-400">*</span></label>
                    <input name="selling_price" type="number" min="0" value="{{ old('selling_price', $product?->selling_price ?? 0) }}" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Stok Awal <span class="text-rose-400">*</span></label>
                    <input name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Stok Minimum <span class="text-rose-400">*</span></label>
                    <input name="min_stock" type="number" min="0" value="{{ old('min_stock', $product?->min_stock ?? 0) }}" required
                           class="w-full rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" name="is_active" type="checkbox" value="1"
                       {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded accent-cyan-400">
                <label for="is_active" class="text-sm text-slate-300">Produk aktif (tampil di kasir)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="rounded-xl bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">
                    {{ $product ? 'Simpan Perubahan' : 'Tambah Produk' }}
                </button>
                <a href="{{ route('produk.index') }}"
                   class="rounded-xl border border-white/10 px-6 py-3 font-semibold hover:border-cyan-400 hover:text-cyan-300">
                    Batal
                </a>
            </div>
        </form>

    </main>
</body>
</html>
