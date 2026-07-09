<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-cyan-300">Inventory</p>
                <h1 class="mt-2 text-3xl font-bold">Kelola Kategori</h1>
                <p class="mt-2 text-sm text-slate-400">Kelompokkan produk agar mudah dicari dan difilter.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.manage') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-cyan-400 hover:text-cyan-300">Kelola Produk</a>
                <button onclick="openCreateModal()" class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">+ Tambah Kategori</button>
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

        <form method="GET" action="{{ route('categories.index') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:flex-row">
            <input
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama kategori..."
                class="min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
            >
            <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Cari</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Jumlah Produk</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-5 py-5">
                                    <div class="font-semibold text-white">{{ $category->name }}</div>
                                    @if ($category->description)
                                        <div class="mt-1 text-sm text-slate-400">{{ Str::limit($category->description, 70) }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-5 text-sm">{{ $category->products_count }} produk</td>
                                <td class="px-5 py-5">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-emerald-400/15 text-emerald-300' : 'bg-slate-400/15 text-slate-400' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-5">
                                    <div class="flex gap-2">
                                        <button
                                            onclick="openEditModal({{ $category->id }}, @js($category->name), @js($category->description ?? ''))"
                                            class="rounded-lg border border-white/10 px-3 py-1.5 text-sm hover:border-cyan-400 hover:text-cyan-300"
                                        >Edit</button>
                                        @if ($category->is_active)
                                            <button
                                                onclick="openDeleteModal({{ $category->id }}, @js($category->name))"
                                                class="rounded-lg border border-white/10 px-3 py-1.5 text-sm hover:border-rose-400 hover:text-rose-300"
                                            >Nonaktifkan</button>
                                        @else
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button class="rounded-lg border border-emerald-400/40 px-3 py-1.5 text-sm hover:border-emerald-400 hover:text-emerald-300">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-400">Kategori tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $categories->links() }}</div>
    </main>

    {{-- Modal Form Kategori (Create / Edit) --}}
    <div id="form-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-slate-900 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 id="form-modal-title" class="text-xl font-bold">Tambah Kategori</h2>
                <button onclick="closeModal('form-modal')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form id="category-form" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-400">Nama Kategori</label>
                        <input name="name" id="f-name" required maxlength="255" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 outline-none focus:border-cyan-400">
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
            <h2 class="mb-2 text-xl font-bold">Nonaktifkan Kategori</h2>
            <p class="mb-6 text-slate-400">Yakin ingin menonaktifkan <strong id="delete-category-name" class="text-white"></strong>?</p>
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

    <script>
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        function openCreateModal() {
            document.getElementById('form-modal-title').textContent = 'Tambah Kategori';
            document.getElementById('category-form').action = '{{ route("categories.store") }}';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('category-form').reset();
            document.getElementById('form-modal').classList.remove('hidden');
        }

        function openEditModal(id, name, desc) {
            document.getElementById('form-modal-title').textContent = 'Edit Kategori';
            document.getElementById('category-form').action = '/categories/' + id;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('f-name').value = name;
            document.getElementById('f-description').value = desc;
            document.getElementById('form-modal').classList.remove('hidden');
        }

        function openDeleteModal(id, name) {
            document.getElementById('delete-category-name').textContent = name;
            document.getElementById('delete-form').action = '/categories/' + id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        document.getElementById('form-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal('form-modal');
        });
        document.getElementById('delete-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal('delete-modal');
        });
    </script>
</body>
</html>
