@extends('layouts.app')

@section('title', 'Manajemen Supplier')
@section('breadcrumb-prefix', 'Master Data')
@section('breadcrumb', 'Supplier')

@section('content')
@php
    $totalSupplier  = $suppliers->count();
    $activeCount    = $suppliers->where('is_active', true)->count();
    $inactiveCount  = $totalSupplier - $activeCount;
@endphp

<!-- ===================== HEADER BANNER ===================== -->

<div class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-8 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)]">

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <p class="text-sm font-semibold text-cyan-400">ERP POS</p>
            <h1 class="mt-1 text-4xl font-extrabold text-white">Manajemen Supplier</h1>
            <p class="mt-2 text-slate-400">Kelola data supplier dan vendor untuk kebutuhan pengadaan barang.</p>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right">
                <p class="text-3xl font-bold text-white">{{ $totalSupplier }}</p>
                <p class="text-xs text-slate-400">Total Supplier</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-emerald-400">{{ $activeCount }}</p>
                <p class="text-xs text-slate-400">Aktif</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-rose-400">{{ $inactiveCount }}</p>
                <p class="text-xs text-slate-400">Tidak Aktif</p>
            </div>
        </div>

    </div>

</div>

<!-- ===================== FLASH MESSAGE ===================== -->

@if (session('success'))
    <div class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-400">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-rose-400">
        {{ session('error') }}
    </div>
@endif

<!-- ===================== TOMBOL TAMBAH ===================== -->

<div class="mt-6 flex justify-end">
    <button
        id="btn-tambah-supplier"
        onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
        class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-cyan-400 transition"
    >
        + Tambah Supplier
    </button>
</div>

<!-- ===================== TABEL SUPPLIER ===================== -->

<div class="mt-4 overflow-x-auto rounded-2xl border border-slate-700 bg-[#0d1b2a]">
    <table class="w-full text-sm text-slate-300">
        <thead class="bg-[#0a1628] text-xs uppercase text-slate-500">
            <tr>
                <th class="px-6 py-4 text-left">#</th>
                <th class="px-6 py-4 text-left">Nama Supplier</th>
                <th class="px-6 py-4 text-left">Kontak Person</th>
                <th class="px-6 py-4 text-left">Telepon</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700/50">
            @forelse ($suppliers as $index => $supplier)
                <tr class="hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ $supplier->name }}</td>
                    <td class="px-6 py-4">{{ $supplier->contact_person ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $supplier->phone ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $supplier->email ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if ($supplier->is_active)
                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400">Aktif</span>
                        @else
                            <span class="rounded-full bg-rose-500/20 px-3 py-1 text-xs font-semibold text-rose-400">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <!-- Tombol Detail -->
                            <button
                                id="btn-detail-{{ $supplier->id }}"
                                data-name="{{ $supplier->name }}"
                                data-contact="{{ $supplier->contact_person ?? '' }}"
                                data-phone="{{ $supplier->phone ?? '' }}"
                                data-email="{{ $supplier->email ?? '' }}"
                                data-address="{{ $supplier->address ?? '' }}"
                                data-active="{{ $supplier->is_active ? '1' : '0' }}"
                                onclick="bukaModalDetail(this)"
                                class="rounded-lg bg-cyan-500/20 px-3 py-1.5 text-xs font-semibold text-cyan-400 transition hover:bg-cyan-500/30"
                            >
                                Detail
                            </button>
                            <!-- Tombol Edit -->
                            <button
                                id="btn-edit-{{ $supplier->id }}"
                                data-id="{{ $supplier->id }}"
                                data-name="{{ $supplier->name }}"
                                data-contact="{{ $supplier->contact_person ?? '' }}"
                                data-phone="{{ $supplier->phone ?? '' }}"
                                data-email="{{ $supplier->email ?? '' }}"
                                data-address="{{ $supplier->address ?? '' }}"
                                data-active="{{ $supplier->is_active ? '1' : '0' }}"
                                onclick="bukaModalEdit(this)"
                                class="rounded-lg bg-amber-500/20 px-3 py-1.5 text-xs font-semibold text-amber-400 hover:bg-amber-500/30 transition btn-edit-supplier"
                            >
                                Edit
                            </button>
                            <!-- Tombol Hapus -->
                            <form
                                id="form-hapus-{{ $supplier->id }}"
                                action="{{ route('suppliers.destroy', $supplier->id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus supplier {{ addslashes($supplier->name) }}?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="rounded-lg bg-rose-500/20 px-3 py-1.5 text-xs font-semibold text-rose-400 hover:bg-rose-500/30 transition"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                        Belum ada data supplier. Klik <strong>Tambah Supplier</strong> untuk menambahkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ===================== MODAL DETAIL ===================== -->

<div
    id="modal-detail"
    role="dialog"
    aria-modal="true"
    aria-labelledby="detail-title"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
    onclick="if (event.target === this) tutupModalDetail()"
>
    <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#0d1b2a] p-8 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-400">Detail Supplier</p>
                <h2 id="detail-title" class="mt-1 text-2xl font-bold text-white">Informasi Supplier</h2>
            </div>
            <button
                type="button"
                aria-label="Tutup detail supplier"
                onclick="tutupModalDetail()"
                class="rounded-lg border border-slate-700 px-3 py-1.5 text-slate-400 transition hover:border-slate-500 hover:text-white"
            >
                &times;
            </button>
        </div>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-800/70 p-4 sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama Supplier</dt>
                <dd id="detail-name" class="mt-1 text-lg font-semibold text-white">-</dd>
            </div>
            <div class="rounded-xl bg-slate-800/70 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kontak Person</dt>
                <dd id="detail-contact" class="mt-1 text-sm text-slate-200">-</dd>
            </div>
            <div class="rounded-xl bg-slate-800/70 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</dt>
                <dd class="mt-1">
                    <span id="detail-status" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">-</span>
                </dd>
            </div>
            <div class="rounded-xl bg-slate-800/70 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Telepon</dt>
                <dd id="detail-phone" class="mt-1 break-words text-sm text-slate-200">-</dd>
            </div>
            <div class="rounded-xl bg-slate-800/70 p-4">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</dt>
                <dd id="detail-email" class="mt-1 break-words text-sm text-slate-200">-</dd>
            </div>
            <div class="rounded-xl bg-slate-800/70 p-4 sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alamat</dt>
                <dd id="detail-address" class="mt-1 whitespace-pre-line text-sm text-slate-200">-</dd>
            </div>
        </dl>

        <div class="mt-6 flex justify-end">
            <button
                type="button"
                onclick="tutupModalDetail()"
                class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-400"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ===================== MODAL TAMBAH ===================== -->

<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#0d1b2a] p-8 shadow-2xl">
        <h2 class="mb-6 text-xl font-bold text-white">Tambah Supplier</h2>
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Nama Supplier <span class="text-rose-400">*</span></label>
                    <input id="tambah-name" type="text" name="name" required placeholder="PT / CV / UD ..."
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Kontak Person</label>
                    <input id="tambah-contact" type="text" name="contact_person" placeholder="Nama PIC"
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-400">Telepon</label>
                        <input id="tambah-phone" type="text" name="phone" placeholder="08xx-xxxx-xxxx"
                            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-400">Email</label>
                        <input id="tambah-email" type="email" name="email" placeholder="email@supplier.com"
                            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Alamat</label>
                    <textarea id="tambah-address" name="address" rows="3" placeholder="Jl. ..."
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="rounded-xl border border-slate-600 px-5 py-2 text-sm text-slate-400 hover:border-slate-500 transition">
                    Batal
                </button>
                <button id="btn-simpan-supplier" type="submit"
                    class="rounded-xl bg-cyan-500 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-400 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL EDIT ===================== -->

<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl border border-slate-700 bg-[#0d1b2a] p-8 shadow-2xl">
        <h2 class="mb-6 text-xl font-bold text-white">Edit Supplier</h2>
        <form id="form-edit-supplier" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Nama Supplier <span class="text-rose-400">*</span></label>
                    <input id="edit-name" type="text" name="name" required
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Kontak Person</label>
                    <input id="edit-contact" type="text" name="contact_person"
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-400">Telepon</label>
                        <input id="edit-phone" type="text" name="phone"
                            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-400">Email</label>
                        <input id="edit-email" type="email" name="email"
                            class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none" />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-400">Alamat</label>
                    <textarea id="edit-address" name="address" rows="3"
                        class="w-full rounded-lg border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white focus:border-cyan-500 focus:outline-none"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <input id="edit-is-active" type="checkbox" name="is_active" value="1"
                        class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-cyan-500 focus:ring-cyan-500" />
                    <label for="edit-is-active" class="text-sm text-slate-400">Supplier Aktif</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="rounded-xl border border-slate-600 px-5 py-2 text-sm text-slate-400 hover:border-slate-500 transition">
                    Batal
                </button>
                <button id="btn-update-supplier" type="submit"
                    class="rounded-xl bg-amber-500 px-5 py-2 text-sm font-semibold text-white hover:bg-amber-400 transition">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function nilaiDetail(value) {
        return value && value.trim() !== '' ? value : '-';
    }

    function bukaModalDetail(button) {
        const isActive = button.dataset.active === '1';
        const status = document.getElementById('detail-status');

        document.getElementById('detail-name').textContent = nilaiDetail(button.dataset.name);
        document.getElementById('detail-contact').textContent = nilaiDetail(button.dataset.contact);
        document.getElementById('detail-phone').textContent = nilaiDetail(button.dataset.phone);
        document.getElementById('detail-email').textContent = nilaiDetail(button.dataset.email);
        document.getElementById('detail-address').textContent = nilaiDetail(button.dataset.address);

        status.textContent = isActive ? 'Aktif' : 'Nonaktif';
        status.className = isActive
            ? 'inline-flex rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400'
            : 'inline-flex rounded-full bg-rose-500/20 px-3 py-1 text-xs font-semibold text-rose-400';

        document.getElementById('modal-detail').classList.remove('hidden');
    }

    function tutupModalDetail() {
        document.getElementById('modal-detail').classList.add('hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            tutupModalDetail();
        }
    });

    /**
     * Buka modal edit dan isi field dengan data supplier yang dipilih.
     */
    function bukaModalEdit(button) {
        const id = button.dataset.id;
        const name = button.dataset.name;
        const contactPerson = button.dataset.contact;
        const phone = button.dataset.phone;
        const email = button.dataset.email;
        const address = button.dataset.address;
        const isActive = button.dataset.active === '1';

        const form = document.getElementById('form-edit-supplier');
        form.action = '/suppliers/' + id;

        document.getElementById('edit-name').value         = name;
        document.getElementById('edit-contact').value      = contactPerson;
        document.getElementById('edit-phone').value        = phone;
        document.getElementById('edit-email').value        = email;
        document.getElementById('edit-address').value      = address;
        document.getElementById('edit-is-active').checked  = isActive;

        document.getElementById('modal-edit').classList.remove('hidden');
    }
</script>
@endsection
