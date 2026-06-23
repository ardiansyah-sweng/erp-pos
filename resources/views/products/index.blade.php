<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Produk</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- Modal Produk -->
<div id="productModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md">

    <div class="w-full max-w-3xl rounded-3xl border border-cyan-400/20 bg-white/10 p-6 shadow-2xl backdrop-blur-2xl">

        <h2 id="modalTitle"
            class="mb-6 text-2xl font-bold text-white">
            Tambah Produk
        </h2>

        <form id="productForm" method="POST">

            @csrf

            <div id="methodField"></div>

            <div class="grid grid-cols-2 gap-4">

                <input
                    type="text"
                    id="sku"
                    name="sku"
                    placeholder="SKU"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="text"
                    id="barcode"
                    name="barcode"
                    placeholder="Barcode"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Nama Produk"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="text"
                    id="unit"
                    name="unit"
                    placeholder="Unit"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="number"
                    id="selling_price"
                    name="selling_price"
                    placeholder="Harga"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    placeholder="Stok"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <input
                    type="number"
                    id="min_stock"
                    name="min_stock"
                    placeholder="Minimum Stok"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:outline-none">

                <select
                    id="is_active"
                    name="is_active"
                    class="rounded-xl border border-white/10 bg-slate-950/50 p-3 text-white focus:border-cyan-400 focus:outline-none">

                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>

                </select>

            </div>

            <div class="mt-6 flex justify-end gap-3">

                <button
                    type="button"
                    onclick="closeModal()"
                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-slate-300 hover:bg-white/10">
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-cyan-500 px-4 py-2 font-semibold text-white transition hover:bg-cyan-600">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

<!-- Modal Delete -->
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md">

    <div class="w-full max-w-md rounded-3xl border border-cyan-400/20 bg-white/10 p-6 shadow-2xl backdrop-blur-2xl ring-1 ring-white/10">

        <h3 class="text-xl font-bold text-white">
            Hapus Produk
        </h3>

        <p id="deleteText"
           class="mt-3 text-slate-300">
        </p>

        <form id="deleteForm" method="POST">

            @csrf
            @method('DELETE')

            <div class="mt-6 flex justify-end gap-3">

                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-slate-300 hover:bg-white/10">
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-rose-500 px-4 py-2 font-semibold text-white transition hover:bg-rose-600">
                    Hapus
                </button>

            </div>

        </form>

    </div>

</div>

const productModal = document.getElementById('productModal');
const productForm = document.getElementById('productForm');
const modalTitle = document.getElementById('modalTitle');
const methodField = document.getElementById('methodField');

        <script>

            function openCreateModal()
            {
                productModal.classList.remove('hidden');
                productModal.classList.add('flex');

                modalTitle.innerText = 'Tambah Produk';

                productForm.action = "{{ route('products.store') }}";

                methodField.innerHTML = '';

                productForm.reset();
            }

            function openEditModal(product)
            {

                productModal.classList.remove('hidden');
                productModal.classList.add('flex');

                modalTitle.innerText = 'Edit Produk';

                productForm.action = '/product-list/' + product.id;

                methodField.innerHTML =
                    '<input type="hidden" name="_method" value="PUT">';

                document.getElementById('sku').value = product.sku;
                document.getElementById('barcode').value = product.barcode;
                document.getElementById('name').value = product.name;
                document.getElementById('unit').value = product.unit;
                document.getElementById('selling_price').value = product.selling_price;
                document.getElementById('stock_quantity').value = product.stock_quantity;
                document.getElementById('min_stock').value = product.min_stock;
                document.getElementById('is_active').value = product.is_active ? 1 : 0;
            }

            function closeModal()
            {
                productModal.classList.remove('flex');
                productModal.classList.add('hidden');
            }

            function openDeleteModal(id, productName)
            {
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');

                deleteForm.action = '/product-list/' + id;

                deleteText.innerText =
                    'Yakin ingin menghapus produk "' + productName + '" ?';
            }

            function closeDeleteModal()
            {
                deleteModal.classList.remove('flex');
                deleteModal.classList.add('hidden');
            }

        </script>

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

    <div class="mt-6 flex justify-end">
      <button
        onclick="openCreateModal()"
        class="rounded-xl bg-emerald-300 px-5 py-3 font-semibold text-slate-950 transition-all duration-300 hover:bg-emerald-300">
        + Tambah Produk
    </button>
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
                        <th class="py-3">Aksi</th>
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
                        <td class="py-3">
                        <div class="flex gap-2">

                            <button
                                onclick='openEditModal(@json($product))'
                                class="rounded-lg bg-amber-500 px-3 py-1 text-sm font-medium text-white hover:bg-amber-600">
                                Edit
                            </button>

                            <button
                                onclick="openDeleteModal({{ $product->id }}, '{{ $product->name }}')"
                                class="rounded-lg bg-rose-500 px-3 py-1 text-sm font-medium text-white hover:bg-rose-600">
                                Hapus
                            </button>

                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-400">
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