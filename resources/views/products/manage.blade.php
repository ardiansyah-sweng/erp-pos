@extends('layouts.app')

@section('title', 'Kelola Produk')
@section('breadcrumb-prefix', 'Inventory')
@section('breadcrumb', 'Produk')

@section('content')

<div class="p-8">

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Kelola Produk</h1>
        <p class="mt-2 text-sm text-slate-400">Tambah, edit, atau nonaktifkan produk.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('pos.index') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-cyan-400 hover:text-cyan-300">Kembali ke POS</a>
        <a href="{{ route('products.export', ['search' => $search]) }}" class="rounded-xl border border-violet-400/40 px-4 py-2 text-center text-sm font-semibold text-violet-300 hover:border-violet-300 hover:text-violet-200">Export CSV</a>
        <button onclick="openCreateModal()" class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">+ Tambah Produk</button>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
        {{ session('success') }}
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

<form method="GET" action="{{ route('products.manage') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row">
    <input
        name="search"
        value="{{ $search }}"
        placeholder="Cari nama, SKU, atau barcode..."
        class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
    >
    <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Cari</button>
</form>

<div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-5 py-4">Produk</th>
                    <th class="px-5 py-4">SKU</th>
                    <th class="px-5 py-4">Harga</th>
                    <th class="px-5 py-4">Stok</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-5">
                            <div class="font-semibold text-white">{{ $product->name }}</div>
                            @if ($product->description)
                                <div class="mt-1 text-sm text-slate-400">{{ Str::limit($product->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-5 text-sm">
                            <div>{{ $product->sku }}</div>
                            @if ($product->barcode)
                                <div class="mt-1 text-xs text-slate-500">Barcode: {{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-5 text-sm">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-5">
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $product->stock_quantity <= $product->min_stock ? 'bg-rose-400/15 text-rose-300' : 'bg-emerald-400/15 text-emerald-300' }}">
                                {{ $product->stock_quantity }} {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-5 py-5">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-400/15 text-emerald-300' : 'bg-slate-400/15 text-slate-400' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-5">
                            <div class="flex gap-2">
                                <a
                                    href="{{ route('products.show', $product->sku) }}"
                                    class="rounded-lg border border-white/10 px-3 py-1.5 text-sm hover:border-cyan-400 hover:text-cyan-300"
                                >Detail</a>
                                <button
                                    onclick="openEditModal({{ $product->id }}, @js($product->name), @js($product->sku), @js($product->barcode), {{ $product->selling_price }}, @js($product->unit), {{ $product->stock_quantity }}, {{ $product->min_stock }}, @js($product->description ?? ''))"
                                    class="rounded-lg border border-white/10 px-3 py-1.5 text-sm hover:border-cyan-400 hover:text-cyan-300"
                                >Edit</button>
                                @if ($product->is_active)
                                    <button
                                        onclick="openDeleteModal({{ $product->id }}, @js($product->name))"
                                        class="rounded-lg border border-white/10 px-3 py-1.5 text-sm hover:border-rose-400 hover:text-rose-300"
                                    >Nonaktifkan</button>
                                @else
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-emerald-400/40 px-3 py-1.5 text-sm hover:border-emerald-400 hover:text-emerald-300">Aktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $products->links() }}</div>

{{-- Modal Form Produk (Create / Edit) --}}
<div id="form-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-slate-900 p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 id="form-modal-title" class="text-xl font-bold">Tambah Produk</h2>
            <button onclick="closeModal('form-modal')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form id="product-form" method="POST" action="{{ route('products.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Nama Produk</label>
                    <input name="name" id="f-name" required maxlength="255" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">SKU</label>
                        <input name="sku" id="f-sku" required maxlength="50" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Barcode</label>
                        <input name="barcode" id="f-barcode" maxlength="100" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Harga Jual (Rp)</label>
                        <input name="selling_price" id="f-selling_price" type="number" min="0" required class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Satuan</label>
                        <input name="unit" id="f-unit" maxlength="20" value="pcs" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Stok Awal</label>
                        <input name="stock_quantity" id="f-stock_quantity" type="number" min="0" required class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Min. Stok</label>
                        <input name="min_stock" id="f-min_stock" type="number" min="0" required class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm text-slate-400">Deskripsi</label>
                    <textarea name="description" id="f-description" maxlength="500" rows="2" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal('form-modal')" class="rounded-xl border border-white/10 px-5 py-3 hover:border-white/20">Batal</button>
                <button type="submit" class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Nonaktifkan --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl border border-white/10 bg-slate-900 p-6">
        <h2 class="mb-2 text-xl font-bold">Nonaktifkan Produk</h2>
        <p class="mb-6 text-slate-400">Yakin ingin menonaktifkan <strong id="delete-product-name" class="text-white"></strong>?</p>
        <form id="delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('delete-modal')" class="rounded-xl border border-white/10 px-5 py-3 hover:border-white/20">Batal</button>
                <button type="submit" class="rounded-xl bg-rose-400 px-5 py-3 font-semibold text-slate-950 hover:bg-rose-300">Ya, Nonaktifkan</button>
            </div>
        </form>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function openCreateModal() {
        document.getElementById('form-modal-title').textContent = 'Tambah Produk';
        document.getElementById('product-form').action = '{{ route("products.store") }}';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('product-form').reset();
        document.getElementById('f-unit').value = 'pcs';
        document.getElementById('form-modal').classList.remove('hidden');
    }

    function openEditModal(id, name, sku, barcode, price, unit, stock, minStock, desc) {
        document.getElementById('form-modal-title').textContent = 'Edit Produk';
        document.getElementById('product-form').action = '/products/manage/' + id;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('f-name').value = name;
        document.getElementById('f-sku').value = sku;
        document.getElementById('f-barcode').value = barcode;
        document.getElementById('f-selling_price').value = price;
        document.getElementById('f-unit').value = unit;
        document.getElementById('f-stock_quantity').value = stock;
        document.getElementById('f-min_stock').value = minStock;
        document.getElementById('f-description').value = desc;
        document.getElementById('form-modal').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-product-name').textContent = name;
        document.getElementById('delete-form').action = '/products/manage/' + id;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    document.getElementById('form-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal('form-modal');
    });
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal('delete-modal');
    });
</script>
@endpush
