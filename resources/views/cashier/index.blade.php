@extends('layouts.app')

@section('title', 'Manajemen Kasir')
@section('breadcrumb-prefix', 'Pengguna')
@section('breadcrumb', 'Kasir')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-6 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)] sm:p-8">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.25em] text-cyan-400">ERP POS</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Manajemen Kasir</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-400 sm:text-base">
                    Kelola akun yang dapat mengakses dan menjalankan transaksi pada sistem kasir.
                </p>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-slate-950/30 px-6 py-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87m-2-12a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $cashiers->count() }}</p>
                    <p class="text-xs uppercase tracking-wider text-slate-400">Total akun kasir</p>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="mt-5 flex items-center gap-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-300" role="status">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-400/20">✓</span>
            {{ session('success') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mt-5 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300" role="alert">
            <p class="font-semibold">Data belum dapat disimpan:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-[#0d1b2a] shadow-xl">
        <div class="flex flex-col gap-4 border-b border-white/10 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Daftar akun kasir</h2>
                <p class="mt-1 text-sm text-slate-400">Username digunakan untuk masuk ke aplikasi.</p>
            </div>
            <button
                type="button"
                id="open-create-modal"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300"
            >
                <span class="text-lg leading-none">+</span>
                Tambah Kasir
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left text-sm text-slate-300">
                <thead class="bg-[#091523] text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">No.</th>
                        <th class="px-6 py-4">Nama Kasir</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Dibuat</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($cashiers as $cashier)
                        <tr class="transition hover:bg-white/[.03]">
                            <td class="px-6 py-4 text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 font-bold text-cyan-300">
                                        {{ mb_strtoupper(mb_substr($cashier->name, 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-white">{{ $cashier->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-lg border border-white/10 bg-slate-950/40 px-3 py-1.5 font-mono text-xs text-slate-300">
                                    {{ $cashier->username }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $cashier->created_at?->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    type="button"
                                    class="edit-cashier rounded-lg border border-amber-400/30 bg-amber-400/10 px-4 py-2 text-xs font-semibold text-amber-300 transition hover:bg-amber-400/20 disabled:cursor-wait disabled:opacity-50"
                                    data-id="{{ $cashier->id }}"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800 text-2xl text-slate-500">♙</div>
                                <p class="mt-4 font-semibold text-slate-300">Belum ada akun kasir</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan akun pertama untuk mulai mengelola kasir.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="create-modal-title">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#0d1b2a] p-6 shadow-2xl sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="create-modal-title" class="text-xl font-bold text-white">Tambah akun kasir</h2>
                    <p class="mt-1 text-sm text-slate-400">Buat identitas dan kredensial kasir baru.</p>
                </div>
                <button type="button" class="close-modal rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white" data-target="create-modal" aria-label="Tutup">✕</button>
            </div>

            <form action="{{ route('cashier.add') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_form" value="create">

                <div>
                    <label for="create-name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama lengkap</label>
                    <input id="create-name" name="name" type="text" value="{{ old('_form') === 'create' ? old('name') : '' }}" required maxlength="255" autocomplete="name" placeholder="Contoh: Siti Rahma"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400">
                </div>

                <div>
                    <label for="create-username" class="mb-1.5 block text-sm font-medium text-slate-300">Username</label>
                    <input id="create-username" name="username" type="text" value="{{ old('_form') === 'create' ? old('username') : '' }}" required maxlength="255" autocomplete="username" placeholder="Contoh: siti.rahma" data-username-input data-result="create-username-result"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400">
                    <p id="create-username-result" class="mt-1.5 min-h-5 text-xs text-slate-500">Gunakan username yang mudah diingat.</p>
                </div>

                <div>
                    <label for="create-password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input id="create-password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400">
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" class="close-modal rounded-xl border border-white/10 px-5 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/5" data-target="create-modal">Batal</button>
                    <button type="submit" class="rounded-xl bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">Simpan Kasir</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#0d1b2a] p-6 shadow-2xl sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="edit-modal-title" class="text-xl font-bold text-white">Edit akun kasir</h2>
                    <p class="mt-1 text-sm text-slate-400">Kosongkan password jika tidak ingin menggantinya.</p>
                </div>
                <button type="button" class="close-modal rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white" data-target="edit-modal" aria-label="Tutup">✕</button>
            </div>

            <p id="edit-load-error" class="mt-5 hidden rounded-xl border border-rose-500/30 bg-rose-500/10 p-3 text-sm text-rose-300"></p>

            <form id="edit-form" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="edit">
                <input id="edit-id" type="hidden" name="cashier_id" value="{{ old('cashier_id') }}">

                <div>
                    <label for="edit-name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama lengkap</label>
                    <input id="edit-name" name="name" type="text" required maxlength="255" autocomplete="name"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-400">
                </div>

                <div>
                    <label for="edit-username" class="mb-1.5 block text-sm font-medium text-slate-300">Username</label>
                    <input id="edit-username" name="username" type="text" required maxlength="255" autocomplete="username" data-username-input data-result="edit-username-result"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-400">
                    <p id="edit-username-result" class="mt-1.5 min-h-5 text-xs text-slate-500">Username harus unik.</p>
                </div>

                <div>
                    <label for="edit-password" class="mb-1.5 block text-sm font-medium text-slate-300">Password baru <span class="font-normal text-slate-500">(opsional)</span></label>
                    <input id="edit-password" name="password" type="password" minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400">
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" class="close-modal rounded-xl border border-white/10 px-5 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/5" data-target="edit-modal">Batal</button>
                    <button type="submit" class="rounded-xl bg-amber-400 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('create-modal');
            const editModal = document.getElementById('edit-modal');
            const editForm = document.getElementById('edit-form');
            const editError = document.getElementById('edit-load-error');
            const checkUsernameUrl = @json(route('cashier.check-username'));
            const editUrlTemplate = @json(route('cashier.edit', ['id' => '__ID__']));
            const updateUrlTemplate = @json(route('cashier.update', ['id' => '__ID__']));

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            document.getElementById('open-create-modal').addEventListener('click', () => openModal(createModal));

            document.querySelectorAll('.close-modal').forEach((button) => {
                button.addEventListener('click', () => closeModal(document.getElementById(button.dataset.target)));
            });

            [createModal, editModal].forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal(modal);
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                [createModal, editModal].forEach((modal) => {
                    if (!modal.classList.contains('hidden')) closeModal(modal);
                });
            });

            document.querySelectorAll('.edit-cashier').forEach((button) => {
                button.addEventListener('click', async () => {
                    button.disabled = true;
                    editError.classList.add('hidden');

                    try {
                        const response = await fetch(editUrlTemplate.replace('__ID__', button.dataset.id), {
                            headers: { 'Accept': 'application/json' },
                        });
                        if (!response.ok) throw new Error('Data kasir tidak dapat dimuat.');

                        const result = await response.json();
                        document.getElementById('edit-id').value = result.data.id;
                        document.getElementById('edit-name').value = result.data.name;
                        document.getElementById('edit-username').value = result.data.username;
                        document.getElementById('edit-password').value = '';
                        editForm.action = updateUrlTemplate.replace('__ID__', result.data.id);
                        openModal(editModal);
                    } catch (error) {
                        editError.textContent = error.message;
                        editError.classList.remove('hidden');
                        openModal(editModal);
                    } finally {
                        button.disabled = false;
                    }
                });
            });

            let usernameTimer;
            document.querySelectorAll('[data-username-input]').forEach((input) => {
                input.addEventListener('input', () => {
                    clearTimeout(usernameTimer);
                    const resultElement = document.getElementById(input.dataset.result);
                    const username = input.value.trim();

                    resultElement.className = 'mt-1.5 min-h-5 text-xs text-slate-500';
                    if (!username) {
                        resultElement.textContent = 'Username wajib diisi.';
                        return;
                    }

                    resultElement.textContent = 'Memeriksa username...';
                    usernameTimer = setTimeout(async () => {
                        const parameters = new URLSearchParams({ username });
                        if (input.id === 'edit-username') {
                            parameters.set('ignore_id', document.getElementById('edit-id').value);
                        }

                        try {
                            const response = await fetch(`${checkUsernameUrl}?${parameters}`, {
                                headers: { 'Accept': 'application/json' },
                            });
                            if (!response.ok) throw new Error();

                            const result = await response.json();
                            resultElement.textContent = result.message;
                            resultElement.className = `mt-1.5 min-h-5 text-xs ${result.available ? 'text-emerald-400' : 'text-rose-400'}`;
                            input.setCustomValidity(result.available ? '' : result.message);
                        } catch {
                            resultElement.textContent = 'Pengecekan username gagal. Validasi dilakukan saat menyimpan.';
                            resultElement.className = 'mt-1.5 min-h-5 text-xs text-amber-400';
                            input.setCustomValidity('');
                        }
                    }, 400);
                });
            });

            @if (isset($errors) && $errors->any() && old('_form') === 'create')
                openModal(createModal);
            @elseif (isset($errors) && $errors->any() && old('_form') === 'edit' && old('cashier_id'))
                document.getElementById('edit-id').value = @json(old('cashier_id'));
                document.getElementById('edit-name').value = @json(old('name'));
                document.getElementById('edit-username').value = @json(old('username'));
                editForm.action = updateUrlTemplate.replace('__ID__', @json(old('cashier_id')));
                openModal(editModal);
            @endif
        });
    </script>
@endpush
