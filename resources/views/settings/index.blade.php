@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('breadcrumb-prefix', 'Pengaturan')
@section('breadcrumb', 'Toko')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-6 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)] sm:p-8">
        <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.25em] text-cyan-400">Konfigurasi lokal</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Pengaturan Toko</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-400 sm:text-base">
                    Atur identitas toko, preferensi transaksi, dan informasi yang ditampilkan pada struk.
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-5 py-4">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                    <i data-lucide="settings-2" class="h-5 w-5"></i>
                </span>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-500">Penyimpanan</p>
                    <p class="mt-1 text-sm font-semibold text-white">Database POS lokal</p>
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

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300" role="alert">
            <p class="font-semibold">Pengaturan belum dapat disimpan:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="mt-6 grid gap-6 xl:grid-cols-3">
        @csrf
        @method('PUT')

        <div class="space-y-6 xl:col-span-2">
            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5 sm:p-6">
                <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                        <i data-lucide="store" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-white">Identitas Toko</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Informasi utama bisnis dan kontak toko.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="store_name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama toko <span class="text-rose-400">*</span></label>
                        <input id="store_name" name="store_name" type="text" value="{{ old('store_name', $setting->store_name) }}" required maxlength="255" autocomplete="organization" data-preview="store-name"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Contoh: Toko Serba Ada">
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-300">Nomor telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $setting->phone) }}" maxlength="30" autocomplete="tel" data-preview="store-phone"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="08xx-xxxx-xxxx">
                    </div>

                    <div>
                        <span class="mb-1.5 block text-sm font-medium text-slate-300">Mata uang</span>
                        <div class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-300">
                            <span>Rupiah Indonesia</span>
                            <span class="rounded-lg bg-cyan-400/10 px-2.5 py-1 text-xs font-bold text-cyan-300">IDR</span>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="mb-1.5 block text-sm font-medium text-slate-300">Alamat toko</label>
                        <textarea id="address" name="address" rows="3" maxlength="1000" autocomplete="street-address" data-preview="store-address"
                            class="w-full resize-none rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Alamat lengkap toko">{{ old('address', $setting->address) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-white/10 bg-[#0d1b2a] p-5 sm:p-6">
                <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-400/10 text-violet-300">
                        <i data-lucide="receipt-text" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-white">Transaksi dan Struk</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pesan untuk pelanggan dan pengingat persediaan.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-5">
                    <div>
                        <label class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                            <span>
                                <span class="block text-sm font-medium text-slate-300">Notifikasi stok rendah</span>
                                <span class="mt-1 block text-xs text-slate-500">Aktifkan pengingat ketika stok mencapai minimum.</span>
                            </span>
                            <span class="relative inline-flex shrink-0 items-center">
                                <input id="low_stock_notification" name="low_stock_notification" type="checkbox" value="1" class="peer sr-only" @checked(old('low_stock_notification', $setting->low_stock_notification))>
                                <span class="h-6 w-11 rounded-full bg-slate-700 transition peer-checked:bg-cyan-500 peer-focus:ring-2 peer-focus:ring-cyan-300/50"></span>
                                <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                            </span>
                        </label>
                    </div>

                    <div>
                        <label for="receipt_footer" class="mb-1.5 block text-sm font-medium text-slate-300">Pesan footer struk</label>
                        <textarea id="receipt_footer" name="receipt_footer" rows="3" maxlength="500" data-preview="receipt-footer"
                            class="w-full resize-none rounded-xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition placeholder:text-slate-600 focus:border-cyan-400" placeholder="Terima kasih telah berbelanja.">{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-white/10 px-5 py-3 text-center text-sm font-semibold text-slate-300 transition hover:bg-white/5">Batal</a>
                <button type="submit" class="rounded-xl bg-cyan-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                    Simpan Pengaturan
                </button>
            </div>
        </div>

        <aside class="xl:col-span-1">
            <div class="sticky top-6 rounded-2xl border border-white/10 bg-[#0d1b2a] p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-white">Preview Struk</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pratinjau informasi pelanggan.</p>
                    </div>
                    <span class="rounded-full bg-emerald-400/10 px-2.5 py-1 text-xs font-semibold text-emerald-300">Live</span>
                </div>

                <div class="mt-5 rounded-xl bg-[#f8fafc] p-5 font-mono text-xs text-slate-700 shadow-inner">
                    <div class="border-b border-dashed border-slate-300 pb-4 text-center">
                        <p id="store-name" class="text-base font-bold uppercase text-slate-950">{{ old('store_name', $setting->store_name) }}</p>
                        <p id="store-address" class="mt-1 whitespace-pre-line text-slate-500">{{ old('address', $setting->address) ?: 'Alamat toko belum diatur' }}</p>
                        <p id="store-phone" class="mt-1 text-slate-500">{{ old('phone', $setting->phone) ?: 'Telepon belum diatur' }}</p>
                    </div>

                    <div class="space-y-1 border-b border-dashed border-slate-300 py-4 text-slate-500">
                        <div class="flex justify-between gap-3"><span>No. Transaksi</span><span class="text-right text-slate-700">TRX-000001</span></div>
                        <div class="flex justify-between gap-3"><span>Tanggal</span><span class="text-right text-slate-700">22/07/2026 20:30</span></div>
                        <div class="flex justify-between gap-3"><span>Kasir</span><span class="text-right text-slate-700">Contoh Kasir</span></div>
                    </div>

                    <div class="space-y-2 border-b border-dashed border-slate-300 py-4">
                        <div>
                            <div class="flex justify-between gap-3"><span>Contoh Produk A</span><span>Rp 30.000</span></div>
                            <p class="mt-0.5 text-slate-400">2 × Rp 15.000</p>
                        </div>
                        <div>
                            <div class="flex justify-between gap-3"><span>Contoh Produk B</span><span>Rp 20.000</span></div>
                            <p class="mt-0.5 text-slate-400">1 × Rp 20.000</p>
                        </div>
                    </div>

                    <div class="space-y-2 border-b border-dashed border-slate-300 py-4">
                        <div class="flex justify-between"><span>Subtotal</span><span>Rp 50.000</span></div>
                        <div class="flex justify-between"><span>Diskon</span><span>- Rp 5.000</span></div>
                        <div class="flex justify-between"><span>Biaya Parkir</span><span>+ Rp 2.000</span></div>
                        <div class="flex justify-between border-t border-dashed border-slate-300 pt-2 font-bold text-slate-950"><span>Total</span><span>Rp 47.000</span></div>
                    </div>

                    <div class="space-y-2 border-b border-dashed border-slate-300 py-4">
                        <div class="flex justify-between"><span>Metode Pembayaran</span><span>TUNAI</span></div>
                        <div class="flex justify-between"><span>Uang Dibayar</span><span>Rp 50.000</span></div>
                        <div class="flex justify-between font-bold text-slate-950"><span>Kembalian</span><span>Rp 3.000</span></div>
                    </div>

                    <p id="receipt-footer" class="whitespace-pre-line pt-4 text-center text-slate-500">{{ old('receipt_footer', $setting->receipt_footer) ?: 'Terima kasih telah berbelanja.' }}</p>
                </div>

                <div class="mt-5 rounded-xl border border-amber-400/20 bg-amber-400/5 p-4 text-xs leading-relaxed text-amber-200/80">
                    Pengaturan disimpan pada database POS lokal dan dapat digunakan pada tampilan struk transaksi.
                </div>
            </div>
        </aside>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fallback = {
                'store-name': 'ERP POS',
                'store-address': 'Alamat toko belum diatur',
                'store-phone': 'Telepon belum diatur',
                'receipt-footer': 'Terima kasih telah berbelanja.',
            };

            document.querySelectorAll('[data-preview]').forEach((input) => {
                input.addEventListener('input', () => {
                    const target = document.getElementById(input.dataset.preview);
                    target.textContent = input.value.trim() || fallback[input.dataset.preview];
                });
            });
        });
    </script>
@endpush
