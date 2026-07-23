@extends('layouts.app')

@section('title', 'Manajemen Supplier')
@section('breadcrumb-prefix', 'Master Data')
@section('breadcrumb', 'Supplier')

@section('content')

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
                <p class="text-3xl font-bold text-white">{{ $supplierSummary['total'] }}</p>
                <p class="text-xs text-slate-400">Total Supplier</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-emerald-400">{{ $supplierSummary['aktif'] }}</p>
                <p class="text-xs text-slate-400">Aktif</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-rose-400">{{ $supplierSummary['nonaktif'] }}</p>
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

<!-- ===================== FILTER & TOMBOL TAMBAH ===================== -->

<div class="mt-6 flex flex-col gap-3 rounded-2xl border border-slate-700 bg-[#0d1b2a] p-4 lg:flex-row lg:items-end">
    <form method="GET" action="{{ route('suppliers.index') }}" class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-end">
        <label class="min-w-0 flex-1">
            <span class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-400">Cari Supplier</span>
            <input
                name="search"
                value="{{ $search }}"
                maxlength="100"
                placeholder="Nama, PIC, telepon, atau email..."
                class="w-full rounded-xl border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white outline-none focus:border-cyan-500"
            >
        </label>
        <label>
            <span class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-400">Status</span>
            <select name="status" class="w-full rounded-xl border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white outline-none focus:border-cyan-500 sm:w-40">
                <option value="semua" @selected($status === 'semua')>Semua</option>
                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
            </select>
        </label>
        <button class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-400">
            Terapkan
        </button>
        @if ($search !== '' || $status !== 'semua')
            <a href="{{ route('suppliers.index') }}" class="rounded-xl border border-slate-600 px-5 py-2.5 text-center text-sm font-semibold text-slate-300 transition hover:border-slate-500 hover:text-white">
                Reset
            </a>
        @endif
    </form>
    <button
        id="btn-tambah-supplier"
        onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
        class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-emerald-400"
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
                        @if ($search !== '' || $status !== 'semua')
                            Supplier tidak ditemukan. Coba ubah kata pencarian atau status.
                        @else
                            Belum ada data supplier. Klik <strong>Tambah Supplier</strong> untuk menambahkan.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
