@extends('layouts.app')

@section('title', 'Detail Produk')
@section('breadcrumb-prefix', 'Inventory')
@section('breadcrumb', 'Detail Produk')

@section('content')

<div class="p-8">

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Detail Produk</h1>
            <p class="mt-2 text-sm text-slate-400">SKU: {{ $sku }}</p>
        </div>
        <a href="{{ route('products.manage') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-cyan-400 hover:text-cyan-300">
            Kembali ke Kelola Produk
        </a>
    </div>

    <div id="productDetailLoading" class="rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
        Memuat data produk...
    </div>

    <div id="productDetailError" class="hidden rounded-2xl border border-rose-400/30 bg-rose-400/10 p-8 text-center text-rose-200"></div>

    <div id="productDetailCard" class="hidden overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="flex flex-col gap-4 border-b border-white/10 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 id="detailName" class="text-2xl font-semibold text-white"></h2>
                <p id="detailDescription" class="mt-1 text-sm text-slate-400"></p>
            </div>
            <span id="detailStockStatus" class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold"></span>
        </div>

        <div class="grid grid-cols-1 gap-px bg-white/10 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">SKU</p>
                <p id="detailSku" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">Barcode</p>
                <p id="detailBarcode" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">Kategori</p>
                <p id="detailCategory" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">Harga Jual</p>
                <p id="detailPrice" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">Stok</p>
                <p id="detailStock" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
            <div class="bg-[#0d1424] p-5">
                <p class="text-xs uppercase tracking-wider text-slate-400">Status Produk</p>
                <p id="detailActive" class="mt-1 text-lg font-semibold text-white"></p>
            </div>
        </div>
    </div>

</div>

<script>
    const sku = @json($sku);

    fetch(`/products/sku/${encodeURIComponent(sku)}`)
        .then(response => response.json().then(body => ({ ok: response.ok, body })))
        .then(({ ok, body }) => {
            document.getElementById('productDetailLoading').classList.add('hidden');

            if (!ok || !body.success) {
                const errorBox = document.getElementById('productDetailError');
                errorBox.textContent = body.message || 'Produk tidak ditemukan.';
                errorBox.classList.remove('hidden');
                return;
            }

            renderProduct(body.data);
        })
        .catch(() => {
            document.getElementById('productDetailLoading').classList.add('hidden');
            const errorBox = document.getElementById('productDetailError');
            errorBox.textContent = 'Gagal memuat data produk. Coba muat ulang halaman.';
            errorBox.classList.remove('hidden');
        });

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID');
    }

    function renderProduct(product) {
        document.getElementById('detailName').textContent = product.name;
        document.getElementById('detailDescription').textContent = product.description || 'Tidak ada deskripsi.';
        document.getElementById('detailSku').textContent = product.sku;
        document.getElementById('detailBarcode').textContent = product.barcode || '-';
        document.getElementById('detailCategory').textContent = product.category ? product.category.name : '-';
        document.getElementById('detailPrice').textContent = formatRupiah(product.selling_price);
        document.getElementById('detailStock').textContent = `${product.stock_quantity} ${product.unit}`;
        document.getElementById('detailActive').textContent = product.is_active ? 'Aktif' : 'Nonaktif';

        const statusBadge = document.getElementById('detailStockStatus');
        const statusMap = {
            aman: ['Stok Aman', 'bg-emerald-400/15 text-emerald-300'],
            menipis: ['Stok Menipis', 'bg-amber-400/15 text-amber-300'],
            habis: ['Stok Habis', 'bg-rose-400/15 text-rose-300'],
        };
        const [label, classes] = statusMap[product.stock_status] || ['-', 'bg-slate-400/15 text-slate-300'];
        statusBadge.textContent = label;
        statusBadge.className = `inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold ${classes}`;

        document.getElementById('productDetailCard').classList.remove('hidden');
    }
</script>

@endsection
