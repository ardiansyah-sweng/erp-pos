@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('breadcrumb-prefix', 'Master Data')
@section('breadcrumb', 'Kategori')

@section('content')
@php
    $totalChildren = $categories->sum(fn($c) => $c->children->count());
    $totalAll = $categories->count() + $totalChildren;
    $activeCount = $categories->where('is_active', true)->count()
        + $categories->sum(fn($c) => $c->children->where('is_active', true)->count());
    $inactiveCount = $totalAll - $activeCount;
    $parentCount = $categories->count();
@endphp

<!-- ===================== HEADER BANNER ===================== -->

<div class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-8 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)]">

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <p class="text-sm font-semibold text-cyan-400">ERP POS</p>
            <h1 class="mt-1 text-4xl font-extrabold text-white">Kategori Produk</h1>
            <p class="mt-2 text-slate-400">Kelola kategori dan subkategori dengan hierarki parent-child.</p>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right">
                <p id="bannerDate" class="text-sm text-slate-300"></p>
                <p id="bannerTime" class="text-3xl font-bold text-cyan-300"></p>
            </div>
            <a href="{{ url('/pos') }}" class="flex items-center gap-2 rounded-xl border border-cyan-500/40 px-5 py-3 font-semibold text-cyan-300 hover:bg-cyan-500/10 transition">
                <i data-lucide="arrow-left" class="w-[18px] h-[18px]"></i>
                Kembali ke Kasir
            </a>
        </div>

    </div>

</div>

<!-- ===================== STAT CARDS ===================== -->

<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

    <div class="stat-card">
        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-cyan-500/15 p-2.5">
                <i data-lucide="layers" class="w-5 h-5 text-cyan-300"></i>
            </div>
            <p class="text-slate-300">Total</p>
        </div>
        <h2 class="mt-3 text-3xl font-bold text-white">{{ $totalAll }}</h2>
        <p class="mt-1 text-xs text-slate-400">Seluruh kategori</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-emerald-500/15 p-2.5">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-300"></i>
            </div>
            <p class="text-slate-300">Aktif</p>
        </div>
        <h2 class="mt-3 text-3xl font-bold text-emerald-300">{{ $activeCount }}</h2>
        <p class="mt-1 text-xs text-slate-400">Kategori berjalan</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-rose-500/15 p-2.5">
                <i data-lucide="x-circle" class="w-5 h-5 text-rose-300"></i>
            </div>
            <p class="text-slate-300">Nonaktif</p>
        </div>
        <h2 class="mt-3 text-3xl font-bold text-rose-300">{{ $inactiveCount }}</h2>
        <p class="mt-1 text-xs text-slate-400">Kategori dinonaktifkan</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-purple-500/15 p-2.5">
                <i data-lucide="folder-tree" class="w-5 h-5 text-purple-300"></i>
            </div>
            <p class="text-slate-300">Parent</p>
        </div>
        <h2 class="mt-3 text-3xl font-bold text-purple-300">{{ $parentCount }}</h2>
        <p class="mt-1 text-xs text-slate-400">Kategori induk</p>
    </div>

</div>

<!-- ===================== FLASH MESSAGES ===================== -->

@if (session('success'))
    <div class="mt-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm font-medium text-emerald-200">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-5 py-4 text-sm font-medium text-rose-200">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-5 py-4 text-sm font-medium text-rose-200">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- ===================== TAMBAH KATEGORI ===================== -->

<div class="card mt-6 p-8">

    <div class="flex items-center gap-4">
        <div class="rounded-xl bg-cyan-500/15 p-3">
            <i data-lucide="plus-circle" class="w-5 h-5 text-cyan-300"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold">Tambah Kategori Baru</h2>
            <p class="text-sm text-slate-400">Buat kategori atau subkategori untuk mengelompokkan produk.</p>
        </div>
    </div>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-5 mt-6 md:grid-cols-3">
            <div>
                <label for="name" class="mb-2 block text-sm text-slate-300">Nama Kategori</label>
                <div class="relative">
                    <i data-lucide="tag" class="input-icon w-[18px] h-[18px]"></i>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input" placeholder="Masukkan nama kategori">
                </div>
            </div>
            <div>
                <label for="parent_id" class="mb-2 block text-sm text-slate-300">Induk (opsional)</label>
                <div class="relative">
                    <i data-lucide="folder-tree" class="input-icon w-[18px] h-[18px]"></i>
                    <select id="parent_id" name="parent_id" class="input appearance-none pr-10">
                        <option value="">-- Tidak ada --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="sort_order" class="mb-2 block text-sm text-slate-300">Urutan</label>
                <div class="relative">
                    <i data-lucide="arrow-up-down" class="input-icon w-[18px] h-[18px]"></i>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="input" placeholder="0">
                </div>
            </div>
        </div>
        <div class="mt-5">
            <label for="description" class="mb-2 block text-sm text-slate-300">Deskripsi (opsional)</label>
            <div class="relative">
                <i data-lucide="align-left" class="input-icon w-[18px] h-[18px]"></i>
                <textarea id="description" name="description" rows="2" class="input" placeholder="Deskripsi singkat">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition">
                <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                Simpan
            </button>
        </div>
    </form>

</div>

<!-- ===================== DAFTAR KATEGORI ===================== -->

<div class="card mt-6 p-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold">Daftar Kategori</h2>
            <p class="text-sm text-slate-400">{{ $totalAll }} kategori tersedia.</p>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10">

        <table class="w-full min-w-[700px]">

            <thead class="bg-cyan-500/10 text-cyan-300">
                <tr>
                    <th class="p-4 text-left w-16">No</th>
                    <th class="text-left">Nama</th>
                    <th class="text-left">Induk</th>
                    <th class="text-left w-20">Urutan</th>
                    <th class="text-left w-28">Status</th>
                    <th class="text-right pr-4 w-32">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/10">
                @forelse ($categories as $index => $category)
                    <tr class="hover:bg-slate-800/60 transition">
                        <td class="p-4 text-slate-400">{{ $index + 1 }}</td>
                        <td class="font-medium text-white">{{ $category->name }}</td>
                        <td class="text-slate-400">{{ $category->parent?->name ?? '-' }}</td>
                        <td class="text-slate-300">{{ $category->sort_order }}</td>
                        <td>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $category->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-right pr-4">
                            <div class="flex items-center justify-end gap-2">
                                <button data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-parent="{{ $category->parent_id }}" data-sort="{{ $category->sort_order }}" data-active="{{ $category->is_active }}" data-description="{{ $category->description }}" onclick="openEdit(this)" class="rounded-lg border border-cyan-500/30 p-2 text-cyan-300 hover:bg-cyan-500/10 transition">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-500/30 p-2 text-rose-400 hover:bg-rose-500/10 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @foreach ($category->children as $childIndex => $child)
                        <tr class="hover:bg-slate-800/60 transition">
                            <td class="p-4 text-slate-400">{{ $index + 1 }}.{{ $childIndex + 1 }}</td>
                            <td class="text-slate-300"><span class="pl-4">↳ {{ $child->name }}</span></td>
                            <td class="text-slate-400">{{ $category->name }}</td>
                            <td class="text-slate-300">{{ $child->sort_order }}</td>
                            <td>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $child->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                    {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-right pr-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button data-id="{{ $child->id }}" data-name="{{ $child->name }}" data-parent="{{ $child->parent_id }}" data-sort="{{ $child->sort_order }}" data-active="{{ $child->is_active }}" data-description="{{ $child->description }}" onclick="openEdit(this)" class="rounded-lg border border-cyan-500/30 p-2 text-cyan-300 hover:bg-cyan-500/10 transition">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $child->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-500/30 p-2 text-rose-400 hover:bg-rose-500/10 transition">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-400">Belum ada kategori. Tambahkan kategori baru di atas.</td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

<!-- ===================== EDIT MODAL ===================== -->

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4 py-6 backdrop-blur-sm">
    <div class="card w-full max-w-md rounded-3xl p-6 shadow-2xl shadow-black/40">

        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Edit Kategori</p>
                <h2 class="mt-1 text-xl font-semibold text-white">Perbarui data kategori</h2>
            </div>
            <button onclick="closeEdit()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:text-white">Tutup</button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="edit_name" class="mb-2 block text-sm text-slate-300">Nama Kategori</label>
                    <input type="text" id="edit_name" name="name" required class="input">
                </div>
                <div>
                    <label for="edit_parent_id" class="mb-2 block text-sm text-slate-300">Induk (opsional)</label>
                    <select id="edit_parent_id" name="parent_id" class="input appearance-none pr-10">
                        <option value="">-- Tidak ada --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_sort_order" class="mb-2 block text-sm text-slate-300">Urutan</label>
                    <input type="number" id="edit_sort_order" name="sort_order" min="0" class="input">
                </div>
                <div>
                    <label for="edit_description" class="mb-2 block text-sm text-slate-300">Deskripsi (opsional)</label>
                    <textarea id="edit_description" name="description" rows="2" class="input"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded border-white/20 bg-slate-950/60 text-cyan-400 focus:ring-cyan-400">
                    <label for="edit_is_active" class="text-sm text-slate-300">Aktif</label>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeEdit()" class="rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:text-white">Batal</button>
                <button type="submit" class="rounded-xl bg-cyan-500 hover:bg-cyan-400 px-5 py-2.5 text-sm font-semibold text-slate-950 transition">Update</button>
            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function bannerClock() {
        const now = new Date();
        document.getElementById("bannerDate").textContent =
            now.toLocaleDateString("id-ID", { weekday:"long", day:"2-digit", month:"long", year:"numeric" });
        document.getElementById("bannerTime").textContent =
            now.toLocaleTimeString("id-ID", { hour:"2-digit", minute:"2-digit", second:"2-digit" });
    }
    bannerClock();
    setInterval(bannerClock, 1000);

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
@endpush
