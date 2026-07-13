@extends('layouts.app')

@section('title', 'Manajemen Kasir')
@section('breadcrumb-prefix', 'Pengaturan /')
@section('breadcrumb', 'Kasir')

@section('content')

<div class="p-8">

    {{-- Header --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Manajemen Kasir</h1>
            <p class="mt-2 text-sm text-slate-400">Kelola akun kasir yang bisa mengakses sistem.</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300 transition-colors">
            + Tambah Kasir
        </button>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-300 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif




    {{-- Tabel --}}
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-5 py-4 w-12">No</th>
                        <th class="px-5 py-4">Nama</th>
                        <th class="px-5 py-4">Username</th>
                        <th class="px-5 py-4">Dibuat</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($cashiers as $index => $cashier)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4 text-sm text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-400 to-teal-500 flex items-center justify-center text-slate-900 font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($cashier->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-white">{{ $cashier->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-300">
                                <span class="font-mono bg-white/5 border border-white/10 px-2 py-1 rounded-md">{{ $cashier->username }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-400">
                                {{ $cashier->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('cashier.edit', $cashier->id) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-400/30 bg-cyan-400/10 px-3 py-1.5 text-sm font-medium text-cyan-300 hover:bg-cyan-400/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <p>Belum ada data kasir.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ===== MODAL TAMBAH KASIR ===== --}}
<div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
         onclick="document.getElementById('modalTambah').classList.add('hidden')"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-md rounded-2xl border border-white/10 bg-[#0d1424] p-6 shadow-2xl">

        <div class="mb-5 flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Tambah Kasir Baru</h2>
                <p class="mt-1 text-sm text-slate-400">Isi data akun kasir yang akan ditambahkan.</p>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="text-slate-500 hover:text-white transition-colors ml-4 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('cashier.add') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Nama kasir"
                       class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Username</label>
                <div class="relative">
                    <input id="inputUsername" type="text" name="username" value="{{ old('username') }}"
                           placeholder="Username untuk login"
                           class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-2.5 pr-11 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                           oninput="checkUsernameAvailability(this.value)" autocomplete="off" required>
                    <span id="usernameStatus" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"></span>
                </div>
                <p id="usernameMsg" class="mt-1.5 text-xs hidden"></p>
                @error('username')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
                <div class="relative">
                    <input id="inputPassword" type="password" name="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-2.5 pr-11 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                           oninput="checkPasswordStrength(); checkPasswordMatch()" required>
                    <button type="button" onclick="togglePassword('inputPassword', 'eyeIcon1')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-cyan-400 transition-colors">
                        <svg id="eyeIcon1" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                {{-- Checklist syarat password --}}
                <div id="pwStrength" class="hidden mt-2 space-y-1">
                    <p class="text-xs font-semibold text-slate-500 mb-1">Syarat password:</p>
                    <div id="rule-min" class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                        Minimal 8 karakter
                    </div>
                    <div id="rule-upper" class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                        Mengandung huruf besar (A-Z)
                    </div>
                    <div id="rule-lower" class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                        Mengandung huruf kecil (a-z)
                    </div>
                    <div id="rule-number" class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                        Mengandung angka (0-9)
                    </div>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <input id="inputPasswordConfirm" type="password" name="password_confirmation"
                           placeholder="Ulangi password"
                           class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-2.5 pr-11 text-sm text-white placeholder-slate-500 outline-none focus:border-cyan-400 transition-colors"
                           oninput="checkPasswordMatch()" required>
                    <button type="button" onclick="togglePassword('inputPasswordConfirm', 'eyeIcon2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-cyan-400 transition-colors">
                        <svg id="eyeIcon2" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p id="passwordMismatch" class="hidden mt-1.5 flex items-center gap-1 text-xs text-rose-400">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Password tidak cocok.
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="flex-1 rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-300 hover:border-white/20 hover:text-white transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSimpan"
                        class="flex-1 rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 hover:bg-cyan-300 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CHECK_URL = '{{ route("cashier.check-username") }}';

    // Buka modal otomatis jika ada error validasi (setelah submit gagal)
    @if($errors->any())
        document.getElementById('modalTambah').classList.remove('hidden');
        // Jalankan cek username jika ada nilai lama
        const oldUsername = document.getElementById('inputUsername')?.value;
        if (oldUsername) checkUsernameAvailability(oldUsername);
    @endif

    // ── Real-time username check (debounce 500ms) ──────────────────────
    let usernameTimer = null;

    function checkUsernameAvailability(value) {
        clearTimeout(usernameTimer);
        const statusEl = document.getElementById('usernameStatus');
        const msgEl    = document.getElementById('usernameMsg');
        const input    = document.getElementById('inputUsername');
        const submitBtn = document.getElementById('btnSimpan');

        value = value.trim();

        if (!value) {
            statusEl.textContent = '';
            msgEl.classList.add('hidden');
            updateSubmitState();
            return;
        }

        // Loading spinner
        statusEl.innerHTML = `<svg class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>`;
        msgEl.classList.add('hidden');

        usernameTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`${CHECK_URL}?username=${encodeURIComponent(value)}`);
                const data = await res.json();

                if (data.available === true) {
                    statusEl.innerHTML = `<svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
                    msgEl.className = 'mt-1.5 text-xs text-emerald-400';
                    msgEl.textContent = '✓ Username tersedia';
                    input.classList.remove('border-rose-500');
                    input.classList.add('border-emerald-500/50');
                } else if (data.available === false) {
                    statusEl.innerHTML = `<svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;
                    msgEl.className = 'mt-1.5 text-xs text-rose-400';
                    msgEl.textContent = '✕ Username sudah digunakan';
                    input.classList.add('border-rose-500');
                    input.classList.remove('border-emerald-500/50');
                }

                msgEl.classList.remove('hidden');
            } catch (e) {
                statusEl.textContent = '';
            }
            updateSubmitState();
        }, 500);
    }

    // ── Toggle show/hide password ──────────────────────────────────────
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }

    // ── Password strength checker ──────────────────────────────────────
    const ICON_OK   = `<svg class="w-3.5 h-3.5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
    const ICON_FAIL = `<svg class="w-3.5 h-3.5 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;
    const ICON_IDLE = `<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>`;

    function setRule(id, passed) {
        const el = document.getElementById(id);
        if (!el) return;
        el.innerHTML = el.innerHTML.replace(/<svg[\s\S]*?<\/svg>/, passed ? ICON_OK : ICON_FAIL);
        el.className = `flex items-center gap-1.5 text-xs ${passed ? 'text-emerald-400' : 'text-rose-400'}`;
    }

    let passwordAllValid = false;

    function checkPasswordStrength() {
        const pw  = document.getElementById('inputPassword').value;
        const box = document.getElementById('pwStrength');

        if (!pw) {
            box.classList.add('hidden');
            passwordAllValid = false;
            updateSubmitState();
            return;
        }

        box.classList.remove('hidden');

        const rules = {
            'rule-min'   : pw.length >= 8,
            'rule-upper' : /[A-Z]/.test(pw),
            'rule-lower' : /[a-z]/.test(pw),
            'rule-number': /[0-9]/.test(pw),
        };

        Object.entries(rules).forEach(([id, ok]) => setRule(id, ok));
        passwordAllValid = Object.values(rules).every(Boolean);
        updateSubmitState();
    }
    function checkPasswordMatch() {
        const pw    = document.getElementById('inputPassword').value;
        const cpw   = document.getElementById('inputPasswordConfirm').value;
        const alert = document.getElementById('passwordMismatch');
        const mismatch = cpw.length > 0 && pw !== cpw;
        alert.classList.toggle('hidden', !mismatch);
        updateSubmitState();
    }

    // ── Disable tombol simpan jika ada error ──────────────────────────
    function updateSubmitState() {
        const btn       = document.getElementById('btnSimpan');
        const userMsg   = document.getElementById('usernameMsg');
        const mismatch  = !document.getElementById('passwordMismatch').classList.contains('hidden');
        const userTaken = userMsg && userMsg.textContent.includes('sudah digunakan');
        const pwWeak    = document.getElementById('inputPassword').value.length > 0 && !passwordAllValid;
        const disabled  = mismatch || userTaken || pwWeak;
        if (btn) {
            btn.disabled = disabled;
            btn.classList.toggle('opacity-50', disabled);
            btn.classList.toggle('cursor-not-allowed', disabled);
        }
    }
</script>
@endpush
