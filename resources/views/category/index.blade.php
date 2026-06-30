<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kategori Produk - ERP POS</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
        } catch (error) {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html[data-theme="light"] body {
            background: #f6f8fb !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] body > .absolute {
            opacity: 0.45;
        }

        html[data-theme="light"] .bg-white\/5 {
            background-color: rgba(255, 255, 255, 0.94) !important;
        }

        html[data-theme="light"] .bg-slate-950,
        html[data-theme="light"] .bg-slate-950\/70,
        html[data-theme="light"] .bg-slate-950\/60,
        html[data-theme="light"] .bg-slate-900\/80,
        html[data-theme="light"] .bg-slate-900\/90 {
            background-color: #ffffff !important;
        }

        html[data-theme="light"] .border-white\/10 {
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .divide-white\/5 > :not([hidden]) ~ :not([hidden]) {
            border-color: #e2e8f0 !important;
        }

        html[data-theme="light"] .text-white {
            color: #0f172a !important;
        }

        html[data-theme="light"] .text-slate-200,
        html[data-theme="light"] .text-slate-300,
        html[data-theme="light"] .text-slate-400,
        html[data-theme="light"] .text-slate-500 {
            color: #64748b !important;
        }

        html[data-theme="light"] .text-cyan-300\/80,
        html[data-theme="light"] .text-cyan-300\/70,
        html[data-theme="light"] .text-cyan-200 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] .text-emerald-300 {
            color: #047857 !important;
        }

        html[data-theme="light"] .text-purple-200 {
            color: #7e22ce !important;
        }

        html[data-theme="light"] input,
        html[data-theme="light"] select,
        html[data-theme="light"] textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] input::placeholder,
        html[data-theme="light"] textarea::placeholder {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

    <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-6 lg:px-8">
        @php
            $totalChildren = $categories->sum(fn($c) => $c->children->count());
            $totalAll = $categories->count() + $totalChildren;
            $activeCount = $categories->where('is_active', true)->count()
                + $categories->sum(fn($c) => $c->children->where('is_active', true)->count());
            $inactiveCount = $totalAll - $activeCount;
            $parentCount = $categories->count();
        @endphp

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">ERP POS</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Kategori Produk</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Kelola kategori produk dan subkategori dengan hierarki parent-child.</p>
                    <a href="{{ route('pos.index') }}" class="mt-4 inline-flex items-center justify-center rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                        Kembali ke POS
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Total</div>
                        <div class="mt-1 text-xl font-semibold text-white">{{ $totalAll }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Aktif</div>
                        <div class="mt-1 text-xl font-semibold text-emerald-300">{{ $activeCount }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Nonaktif</div>
                        <div class="mt-1 text-xl font-semibold text-slate-300">{{ $inactiveCount }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Parent</div>
                        <div class="mt-1 text-xl font-semibold text-cyan-300">{{ $parentCount }}</div>
                    </div>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-medium text-emerald-100">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm font-medium text-rose-100">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm font-medium text-rose-100">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl shadow-2xl shadow-black/30">
            <h2 class="text-lg font-semibold text-white mb-4">Tambah Kategori Baru</h2>
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-1">Nama Kategori</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400"
                            placeholder="Masukkan nama kategori">
                    </div>
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-slate-300 mb-1">Induk (opsional)</label>
                        <select id="parent_id" name="parent_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-cyan-400">
                            <option value="">-- Tidak ada --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-slate-300 mb-1">Urutan</label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-1">Deskripsi (opsional)</label>
                    <textarea id="description" name="description" rows="2"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400"
                        placeholder="Deskripsi singkat">{{ old('description') }}</textarea>
                </div>
                <button type="submit"
                    class="rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
                    Simpan
                </button>
            </form>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl shadow-2xl shadow-black/30">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Daftar Kategori</h2>
                <span class="text-sm text-slate-400">{{ $totalAll }} kategori</span>
            </div>
            <div class="overflow-hidden rounded-2xl border border-white/10">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-900/90 text-left text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-4 py-3 font-medium">No</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Induk</th>
                            <th class="px-4 py-3 font-medium">Urutan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-slate-950/60">
                        @forelse ($categories as $index => $category)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-white">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-slate-400">{{ $category->parent?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $category->sort_order }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $category->is_active ? 'border border-emerald-400/30 bg-emerald-400/10 text-emerald-300' : 'border border-rose-400/30 bg-rose-400/10 text-rose-300' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-parent="{{ $category->parent_id }}" data-sort="{{ $category->sort_order }}" data-active="{{ $category->is_active }}" data-description="{{ $category->description }}" onclick="openEdit(this)"
                                            class="text-cyan-300 hover:text-cyan-200 text-xs font-medium transition-colors">Edit</button>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-300 hover:text-rose-200 text-xs font-medium transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($category->children as $childIndex => $child)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 text-slate-400">{{ $index + 1 }}.{{ $childIndex + 1 }}</td>
                                    <td class="px-4 py-3 pl-8 text-slate-300">↳ {{ $child->name }}</td>
                                    <td class="px-4 py-3 text-slate-400">{{ $category->name }}</td>
                                    <td class="px-4 py-3 text-slate-300">{{ $child->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $child->is_active ? 'border border-emerald-400/30 bg-emerald-400/10 text-emerald-300' : 'border border-rose-400/30 bg-rose-400/10 text-rose-300' }}">
                                            {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <button data-id="{{ $child->id }}" data-name="{{ $child->name }}" data-parent="{{ $child->parent_id }}" data-sort="{{ $child->sort_order }}" data-active="{{ $child->is_active }}" data-description="{{ $child->description }}" onclick="openEdit(this)"
                                                class="text-cyan-300 hover:text-cyan-200 text-xs font-medium transition-colors">Edit</button>
                                            <form action="{{ route('categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $child->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-300 hover:text-rose-200 text-xs font-medium transition-colors">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-950 p-6 text-slate-100 shadow-2xl shadow-black/40">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Edit Kategori</p>
                    <h2 class="mt-1 text-xl font-semibold text-white">Perbarui data kategori</h2>
                </div>
                <button onclick="closeEdit()" class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:text-white">Tutup</button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-slate-300 mb-1">Nama Kategori</label>
                    <input type="text" id="edit_name" name="name" required
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label for="edit_parent_id" class="block text-sm font-medium text-slate-300 mb-1">Induk (opsional)</label>
                    <select id="edit_parent_id" name="parent_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-cyan-400">
                        <option value="">-- Tidak ada --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_sort_order" class="block text-sm font-medium text-slate-300 mb-1">Urutan</label>
                    <input type="number" id="edit_sort_order" name="sort_order" min="0"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label for="edit_description" class="block text-sm font-medium text-slate-300 mb-1">Deskripsi (opsional)</label>
                    <textarea id="edit_description" name="description" rows="2"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-cyan-400"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded border-white/20 bg-slate-950/60 text-cyan-400 focus:ring-cyan-400">
                    <label for="edit_is_active" class="text-sm text-slate-300">Aktif</label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEdit()"
                        class="rounded-2xl border border-white/10 bg-slate-950/70 px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:text-white">Batal</button>
                    <button type="submit"
                        class="rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:brightness-110">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');

        function openEdit(btn) {
            const id = btn.dataset.id;
            editForm.action = '/categories/' + id;
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_parent_id').value = btn.dataset.parent || '';
            document.getElementById('edit_sort_order').value = btn.dataset.sort;
            document.getElementById('edit_description').value = btn.dataset.description || '';
            document.getElementById('edit_is_active').checked = btn.dataset.active === '1';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEdit() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeEdit();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeEdit();
        });
    </script>
</body>
</html>
