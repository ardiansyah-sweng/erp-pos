@extends('layouts.app')

@section('title', 'Penyesuaian Stok')
@section('breadcrumb-prefix', 'Inventory')
@section('breadcrumb', 'Penyesuaian Stok')

@section('content')

<div class="p-8">

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Penyesuaian Stok</h1>
        <p class="mt-2 text-sm text-slate-400">Tambah, kurangi, atau koreksi stok produk secara manual.</p>
    </div>
    <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('stock-opname.print') }}" class="rounded-xl bg-emerald-400 px-4 py-2 text-center text-sm font-semibold text-slate-950 hover:bg-emerald-300">
            Cetak Stok Opname
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

@if ($errors->any())
    <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-200">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="GET" action="{{ route('stock-adjustments.index') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row">
    <input
        name="search"
        value="{{ $search }}"
        placeholder="Cari nama, SKU, atau barcode..."
        class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
    >
    <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Cari Produk</button>
</form>

<div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
    <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
        <h2 class="text-lg font-semibold">Daftar Produk</h2>
        <a href="{{ route('products.manage') }}" class="rounded-lg bg-cyan-400 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-cyan-300">+ Product</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-5 py-4">Produk</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Stok</th>
                    <th class="px-5 py-4">Penyesuaian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($products as $product)
                    @php($isLowStock = $product->stock_quantity <= $product->min_stock)
                    <tr class="align-top">
                        <td class="px-5 py-5">
                            <div class="font-semibold text-white">{{ $product->name }}</div>
                            <div class="mt-1 text-sm text-slate-400">{{ $product->sku }} · {{ $product->barcode ?: 'Tanpa barcode' }}</div>
                        </td>
                        <td class="px-5 py-5">
                            @if ($product->stock_status === 'habis')
                                <span class="inline-flex rounded-full bg-rose-400/15 px-3 py-1 text-sm font-semibold text-rose-300">Habis</span>
                            @elseif ($product->stock_status === 'menipis')
                                <span class="inline-flex rounded-full bg-amber-400/15 px-3 py-1 text-sm font-semibold text-amber-300">Menipis</span>
                            @else
                                <span class="inline-flex rounded-full bg-emerald-400/15 px-3 py-1 text-sm font-semibold text-emerald-300">Stok Penuh</span>
                            @endif
                        </td>
                        <td class="px-5 py-5">
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $isLowStock ? 'bg-rose-400/15 text-rose-300' : 'bg-emerald-400/15 text-emerald-300' }}">
                                {{ $product->stock_quantity }} {{ $product->unit }}
                            </span>
                            <div class="mt-2 text-xs text-slate-500">Minimum: {{ $product->min_stock }}</div>
                        </td>
                        <td class="px-5 py-5">
                            <form method="POST" action="{{ route('stock-adjustments.update', $product) }}" class="grid min-w-[34rem] grid-cols-12 gap-2">
                                @csrf
                                @method('PUT')
                                <select name="movement_type" class="col-span-3 rounded-lg border border-white/10 bg-slate-900 px-3 py-2 text-sm">
                                    <option value="in">Stok Masuk</option>
                                    <option value="out">Stok Keluar</option>
                                    <option value="adjustment">Koreksi +/-</option>
                                </select>
                                <input name="quantity" type="number" step="1" required placeholder="Jumlah" class="col-span-2 rounded-lg border border-white/10 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-cyan-400">
                                <input name="notes" maxlength="255" placeholder="Alasan penyesuaian" class="col-span-5 rounded-lg border border-white/10 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-cyan-400">
                                <button class="col-span-2 rounded-lg bg-emerald-400 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-300">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $products->links() }}</div>

</div>

@endsection
