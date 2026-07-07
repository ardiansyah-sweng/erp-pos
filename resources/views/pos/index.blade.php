<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ERP POS</title>
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

        html[data-theme="light"] .bg-white\/10 {
            background-color: #f1f5f9 !important;
        }

        html[data-theme="light"] .bg-slate-950\/70,
        html[data-theme="light"] .bg-slate-950\/60,
        html[data-theme="light"] .bg-slate-950\/50 {
            background-color: #ffffff !important;
        }

        html[data-theme="light"] .bg-slate-900\/80,
        html[data-theme="light"] .bg-slate-900\/90,
        html[data-theme="light"] .bg-slate-900\/95 {
            background-color: #f8fafc !important;
        }

        html[data-theme="light"] .bg-slate-950 {
            background-color: #ffffff !important;
        }

        html[data-theme="light"] .border-white\/10 {
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .border-x {
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

        html[data-theme="light"] .text-slate-200 {
            color: #334155 !important;
        }

        html[data-theme="light"] .text-rose-300 {
            color: #e11d48 !important;
        }

        html[data-theme="light"] .text-amber-300 {
            color: #b45309 !important;
        }

        html[data-theme="light"] .text-cyan-300\/80,
        html[data-theme="light"] .text-cyan-300\/70,
        html[data-theme="light"] .text-cyan-200 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] .text-emerald-300 {
            color: #047857 !important;
        }

        html[data-theme="light"] #clockTime,
        html[data-theme="light"] #clockIcon svg {
            color: #0369a1 !important;
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

        /* Sort menu – tema terang */
        html[data-theme="light"] #sortMenuToggle {
            background-color: #ffffff !important;
            border-color: #dbe3ea !important;
            color: #334155 !important;
        }

        html[data-theme="light"] #sortMenuToggle:hover {
            border-color: #06b6d4 !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] #sortMenu {
            background-color: #ffffff !important;
            border-color: #dbe3ea !important;
            box-shadow: 0 8px 24px rgba(15,23,42,0.10) !important;
        }

        html[data-theme="light"] #sortMenu .sort-option {
            color: #475569 !important;
        }

        html[data-theme="light"] #sortMenu .sort-option:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] #sortMenu .sort-option.text-cyan-300 {
            background-color: #ecfeff !important;
            color: #0e7490 !important;
        }

        html[data-theme="light"] #sortMenu .sort-option .text-slate-500 {
            color: #94a3b8 !important;
        }

        html[data-theme="light"] #sortMenu div.text-slate-500 {
            color: #94a3b8 !important;
        }

        html[data-theme="light"] #sortDirBadge {
            background-color: #ecfeff !important;
            border-color: #67e8f9 !important;
            color: #0e7490 !important;
        }

        html[data-theme="light"] #sortStatusLabel {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>
    <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-6 lg:px-8">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Point of Sales</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Kasir cepat untuk transaksi harian.</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Cari atau scan produk, cek isi keranjang, lalu selesaikan pembayaran tanpa reload halaman.</p>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <a href="{{ route('stock-adjustments.index') }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-400/15 px-4 py-2 text-sm font-medium text-emerald-300 transition hover:border-emerald-300 hover:bg-emerald-400/25 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>
                            </svg>
                            Penyesuaian Stok
                        </a>
                        <button id="themeToggle" type="button" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white" aria-pressed="false">
                            <span id="themeIcon" aria-hidden="true" class="inline-flex h-4 w-4"></span>
                            <span id="themeLabel">Mode terang</span>
                        </button>
                        <a href="{{ route('sales-notes') }}" class="inline-flex items-center gap-2 rounded-full border border-cyan-400/40 bg-cyan-400/15 px-4 py-2 text-sm font-medium text-cyan-200 transition hover:border-cyan-300 hover:bg-cyan-400/25 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 3v18h18"/>
                                <path d="m19 9-5 5-4-4-3 3"/>
                            </svg>
                            Laporan Penjualan
                        </a>
                        <a href="{{ route('returns.index') }}" class="inline-flex items-center gap-2 rounded-full border border-rose-400/40 bg-rose-400/15 px-4 py-2 text-sm font-medium text-rose-200 transition hover:border-rose-300 hover:bg-rose-400/25 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m9 14-4-4 4-4"/>
                                <path d="M5 10h11a4 4 0 0 1 0 8h-1"/>
                            </svg>
                            Retur Transaksi
                        </a>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-3">
                    <div class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm">
                        <div class="flex items-center gap-1.5 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            <span id="clockDate">-</span>
                        </div>
                        <div class="h-4 w-px bg-white/20"></div>
                        <div class="flex items-center gap-1.5 text-cyan-300">
                            <span id="clockIcon" class="inline-flex"></span>
                            <span id="clockTime" class="font-mono font-semibold tracking-wide">--:--:--</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                            <div class="text-slate-400">Produk</div>
                            <div id="productCount" class="mt-1 text-xl font-semibold text-white">0</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                            <div class="text-slate-400">Keranjang</div>
                            <div id="cartCount" class="mt-1 text-xl font-semibold text-white">0</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                            <div class="text-slate-400">Subtotal</div>
                            <div id="subtotalLabel" class="mt-1 text-xl font-semibold text-white">Rp0</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                            <div class="text-slate-400">Total</div>
                            <div id="totalLabel" class="mt-1 text-xl font-semibold text-emerald-300">Rp0</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-sm text-slate-300" for="productSearch">Cari produk</label>
                            <input id="productSearch" type="text" placeholder="Nama, SKU, atau barcode" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none ring-0 placeholder:text-slate-500 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="barcodeSearch">Scan / ketik barcode</label>
                            <div class="mt-2 flex gap-3">
                                <input id="barcodeSearch" type="text" placeholder="Tekan Enter setelah scan" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                                <button id="refreshProducts" class="rounded-2xl border border-cyan-400/40 bg-cyan-400/15 px-4 py-3 text-cyan-200 transition hover:bg-cyan-400/25">Reload</button>
                            </div>
                        </div>
                    </div>
                    <p id="productsStatus" class="mt-3 text-sm text-slate-400">Memuat produk...</p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-white">Daftar Produk</h2>
                        <div class="flex items-center gap-2">
                            <span id="productsMeta" class="text-sm text-slate-400"></span>
                            <!-- Sort dropdown trigger -->
                            <div class="relative">
                                <button id="sortMenuToggle" type="button" class="flex items-center gap-2 rounded-xl border border-white/10 bg-slate-950/70 px-3 py-1.5 text-sm text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                                    <span id="sortByLabel">Nama</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                                <div id="sortMenu" class="absolute right-0 z-20 mt-1 hidden w-48 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/95 shadow-xl backdrop-blur-xl">
                                    <div class="px-3 py-2 text-xs uppercase tracking-widest text-slate-500">Urutkan berdasarkan:</div>
                                    <button type="button" data-sort-by="name" data-sort-dir="asc" class="sort-option flex w-full items-center justify-between px-4 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                                        <span>Nama (A → Z)</span><span class="text-xs text-slate-500">A→Z</span>
                                    </button>
                                    <button type="button" data-sort-by="name" data-sort-dir="desc" class="sort-option flex w-full items-center justify-between px-4 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                                        <span>Nama (Z → A)</span><span class="text-xs text-slate-500">Z→A</span>
                                    </button>
                                    <button type="button" data-sort-by="selling_price" data-sort-dir="asc" class="sort-option flex w-full items-center justify-between px-4 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                                        <span>Harga (Termurah)</span><span class="text-xs text-slate-500">Rp↑</span>
                                    </button>
                                    <button type="button" data-sort-by="selling_price" data-sort-dir="desc" class="sort-option flex w-full items-center justify-between px-4 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                                        <span>Harga (Termahal)</span><span class="text-xs text-slate-500">Rp↓</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Sort dir badge -->
                            <span id="sortDirBadge" class="rounded-xl border border-cyan-400/30 bg-cyan-400/10 px-3 py-1.5 text-xs font-semibold text-cyan-300">A→Z</span>
                        </div>
                    </div>
                    <p id="sortStatusLabel" class="mb-3 hidden text-xs text-slate-500"></p>
                    <div id="productGrid" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
                </div>
            </div>

            <aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Keranjang</h2>
                        <button id="clearCart" class="text-sm text-rose-300 transition hover:text-rose-200">Kosongkan</button>
                    </div>
                    <div id="cartEmptyState" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/50 px-4 py-8 text-center text-sm text-slate-400">
                        Keranjang masih kosong. Pilih produk atau scan barcode untuk mulai transaksi.
                    </div>
                    <div id="cartTableWrapper" class="hidden overflow-hidden rounded-2xl border border-white/10">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-900/90 text-slate-400">
                                <tr>
                                    <th class="px-3 py-3">Produk</th>
                                    <th class="px-3 py-3">Qty</th>
                                    <th class="px-3 py-3">Total</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable" class="divide-y divide-white/5 bg-slate-950/60"></tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <h2 class="text-lg font-semibold text-white">Pembayaran</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="text-sm text-slate-300" for="discountAmount">Diskon</label>
                            <div class="mt-2 flex overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70 focus-within:border-cyan-400">
                                <input id="discountAmount" type="text" inputmode="numeric" value="Rp 0"
                                    class="min-w-0 flex-1 bg-transparent px-4 py-3 text-white outline-none placeholder:text-slate-500"
                                    aria-label="Jumlah diskon">
                                <div class="flex-shrink-0 border-l border-white/10">
                                    <select id="discountType"
                                        class="h-full appearance-none bg-slate-900/80 px-3 py-3 text-sm font-medium text-slate-200 outline-none cursor-pointer hover:bg-slate-800/80 focus:bg-slate-800/80 transition-colors"
                                        aria-label="Tipe diskon">
                                        <option value="nominal">Rp</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="paymentMethod">Metode pembayaran</label>
                            <select id="paymentMethod" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-cyan-400">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="e_wallet">E-wallet</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div id="ewalletPanel" class="hidden">
                            <label class="text-sm text-slate-300">Pilih E-Wallet</label>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <button type="button" data-ewallet="dana" data-name="DANA"
                                    class="ewallet-btn relative flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-left transition hover:border-cyan-400/50">
                                    <img src="/images/dana.png" alt="DANA" class="h-8 w-8 object-contain">
                                    <span class="text-sm font-medium text-white">DANA</span>
                                    <span class="ewallet-check absolute right-2 top-2 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                </button>
                                <button type="button" data-ewallet="gopay" data-name="GoPay"
                                    class="ewallet-btn relative flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-left transition hover:border-cyan-400/50">
                                    <img src="/images/gopay.png" alt="GoPay" class="h-8 w-8 object-contain">
                                    <span class="text-sm font-medium text-white">GoPay</span>
                                    <span class="ewallet-check absolute right-2 top-2 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                </button>
                                <button type="button" data-ewallet="ovo" data-name="OVO"
                                    class="ewallet-btn relative flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-left transition hover:border-cyan-400/50">
                                    <img src="/images/ovo.png" alt="OVO" class="h-8 w-8 object-contain">
                                    <span class="text-sm font-medium text-white">OVO</span>
                                    <span class="ewallet-check absolute right-2 top-2 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                </button>
                                <button type="button" data-ewallet="shopeepay" data-name="ShopeePay"
                                    class="ewallet-btn relative flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-left transition hover:border-cyan-400/50">
                                    <img src="/images/shopeepay.png" alt="ShopeePay" class="h-8 w-8 object-contain">
                                    <span class="text-sm font-medium text-white">ShopeePay</span>
                                    <span class="ewallet-check absolute right-2 top-2 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400"><path d="M20 6 9 17l-5-5"/></svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                       <div id="cardForm" class="hidden mt-4 space-y-3">
                        <div>
                            <label class="text-sm text-slate-300">Nama Pemegang Kartu</label>
                            <input id="cardHolder" type="text" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white">
                        </div>

                        <div>
                            <label class="text-sm text-slate-300">No Kartu (4 digit terakhir)</label>
                            <input id="cardNumber" type="text" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white">
                        </div>

                        <div>
                            <label class="text-sm text-slate-300">Bank / Provider</label>
                            <input id="cardBank" type="text" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white">
                        </div>

                        <div>
                            <label class="text-sm text-slate-300">Kode Approval</label>
                            <input id="approvalCode" type="text" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white">
                        </div>
                        </div>


                        <div id="cashTenderedWrapper">
                            <label class="text-sm text-slate-300" for="cashTendered">Uang dibayar</label>
                            <input id="cashTendered" type="text" inputmode="numeric" value="Rp 0" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="notes">Catatan</label>
                            <textarea id="notes" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-5">

                        <label class="mb-2 block text-sm font-medium text-white">
                            Cari Member
                        </label>

                        <input
                            type="text"
                            id="memberSearch"
                            placeholder="Cari nomor HP / 4 digit terakhir"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 p-3 text-white placeholder:text-slate-400">

                        <div
                            id="memberResult"
                            class="mt-3 space-y-2">
                        </div>

                        <a
                            href="{{ route('members.index') }}"
                            class="mt-3 inline-block rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700">

                            + Tambah Member Baru

                        </a>

                    </div>

                    <div class="mt-5 grid gap-3 rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-sm text-slate-300">
                        <div class="flex items-center justify-between"><span>Subtotal</span><span id="subtotalValue" class="font-semibold text-white">Rp0</span></div>
                        <div class="flex items-center justify-between"><span>Diskon</span><span id="discountValue" class="font-semibold text-white">Rp0</span></div>
                        <div class="flex items-center justify-between"><span>Total</span><span id="grandTotalValue" class="font-semibold text-emerald-300">Rp0</span></div>
                        <div id="changeRow" class="flex items-center justify-between"><span>Kembalian</span><span id="changeValue" class="font-semibold text-cyan-300">Rp0</span></div>
                    </div>

                    <button id="checkoutButton" class="mt-5 w-full rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50" disabled>Checkout</button>
                    <div id="checkoutStatus" class="mt-3 hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Riwayat Transaksi</h2>
                            <span id="transactionsMeta" class="text-sm text-slate-400">0 transaksi</span>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="rounded-full border border-white/10 bg-slate-950/60 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                            Lihat semua
                        </a>
                    </div>
                    <div id="transactionHistory" class="space-y-3">
                        <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 p-4 text-sm text-slate-400">
                            Belum ada transaksi.
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <!-- QRIS Payment Modal -->
    <div id="qrisModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="relative mx-auto w-full max-w-sm rounded-3xl border border-white/10 bg-slate-900 shadow-2xl shadow-black/60 overflow-hidden max-h-[92vh] flex flex-col">
            <!-- Header -->
            <div class="flex-shrink-0 bg-gradient-to-r from-emerald-500/20 via-cyan-500/15 to-transparent px-6 py-4 text-center border-b border-white/10">
                <p class="text-xs uppercase tracking-[0.35em] text-cyan-300/80">Pembayaran</p>
                <h2 class="mt-0.5 text-2xl font-bold text-white">QRIS</h2>
            </div>

            <!-- Scrollable body -->
            <div class="overflow-y-auto flex-1 px-6 py-4 space-y-3">
                <!-- QR Image -->
                <div class="flex justify-center">
                    <div class="rounded-2xl border-2 border-white/20 bg-white p-3 shadow-xl w-60">
                        <img id="qrisImage" src="/images/qris.png" alt="QRIS Payment Code"
                             class="w-full aspect-[3/4] object-contain"
                             onerror="this.style.display='none';document.getElementById('qrisFallback').style.display='flex'">
                        <div id="qrisFallback" style="display:none"
                             class="w-full aspect-[3/4] flex-col items-center justify-center rounded-xl bg-slate-100 text-slate-500 text-xs text-center gap-2 px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-400">
                                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="3" height="3"/><rect x="18" y="14" width="3" height="3"/><rect x="14" y="18" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/>
                            </svg>
                            <span>Simpan gambar QRIS<br>di <b>public/images/qris.png</b></span>
                        </div>
                    </div>
                </div>

                <!-- Item list -->
                <div id="qrisItemsList" class="divide-y divide-white/5 rounded-2xl border border-white/10 bg-slate-950/60 text-sm text-slate-300 overflow-hidden"></div>

                <!-- Summary -->
                <div class="grid gap-1.5 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-300">
                    <div class="flex justify-between"><span>Subtotal</span><span id="qrisSubtotal" class="font-semibold text-white">Rp0</span></div>
                    <div class="flex justify-between"><span>Diskon</span><span id="qrisDiscount" class="font-semibold text-white">Rp0</span></div>
                    <div class="flex justify-between border-t border-white/10 pt-1.5 mt-0.5">
                        <span class="font-semibold text-white">Total</span>
                        <span id="qrisAmount" class="text-lg font-bold text-emerald-300">Rp0</span>
                    </div>
                </div>

                <!-- Timer -->
                <div class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-center">
                    <p class="text-sm text-slate-400">Menunggu pembayaran</p>
                    <p id="qrisTimer" class="mt-1 text-3xl font-bold tabular-nums tracking-wider text-white">01:00</p>
                    <p class="mt-1 text-xs text-slate-500">Selesaikan pembayaran sebelum waktu habis</p>
                </div>
            </div>

            <!-- Fixed footer buttons -->
            <div class="flex-shrink-0 px-6 pb-5 pt-3 space-y-2 border-t border-white/10 bg-slate-900">
                <button id="checkQrisButton" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-60">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/>
                    </svg>
                    Cek Pembayaran
                </button>
                <button id="cancelQrisButton" class="w-full rounded-2xl px-4 py-2.5 text-sm text-slate-400 transition hover:text-white">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <div id="checkoutConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm">
        <div class="w-full max-w-2xl overflow-hidden rounded-3xl border border-white/10 bg-slate-950 text-slate-100 shadow-2xl shadow-black/40">
            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-5 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Konfirmasi Checkout</p>
                    <h2 class="mt-1 text-xl font-semibold text-white">Periksa kembali pesanan</h2>
                </div>
                <button id="closeCheckoutConfirmModal" type="button" class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:text-white">Batal</button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto px-5 py-4">
                <div id="checkoutConfirmItems" class="space-y-3"></div>
                <div class="mt-4 rounded-3xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between"><span>Subtotal</span><span id="checkoutConfirmSubtotal" class="font-semibold text-white">Rp0</span></div>
                    <div class="mt-2 flex items-center justify-between"><span>Diskon</span><span id="checkoutConfirmDiscount" class="font-semibold text-white">Rp0</span></div>
                    <div class="mt-2 border-t border-white/10 pt-3 flex items-center justify-between text-base font-semibold text-emerald-300"><span>Total</span><span id="checkoutConfirmTotal">Rp0</span></div>
                    <div class="mt-2 text-sm text-slate-400"><span>Metode: </span><span id="checkoutConfirmPayment">-</span></div>
                </div>
            </div>
            <div class="flex flex-col gap-3 border-t border-white/10 bg-slate-900 px-5 py-4">
                <button id="confirmCheckoutButton" type="button" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110">Konfirmasi</button>
                <button id="confirmCheckoutAndPrintButton" type="button" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-400/40 bg-emerald-400/10 px-4 py-3 font-semibold text-emerald-200 transition hover:bg-emerald-400/20">Konfirmasi & Cetak Struk</button>
                <button id="cancelCheckoutButton" type="button" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-slate-300 transition hover:text-white">Kembali</button>
            </div>
        </div>
    </div>

    <div id="transactionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-950 p-5 text-slate-100 shadow-2xl shadow-black/40">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Detail Transaksi</p>
                    <h2 id="transactionModalTitle" class="mt-1 text-xl font-semibold text-white">TRX-0000</h2>
                    <p id="transactionModalDate" class="mt-1 text-sm text-slate-400">-</p>
                </div>
                <button id="closeTransactionModal" type="button" class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:text-white">Tutup</button>
            </div>
            <div id="transactionModalItems" class="divide-y divide-white/5 rounded-2xl border border-white/10 bg-slate-950/60"></div>
            <div id="transactionModalPayment" class="mt-4 grid gap-2 rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-300"></div>
            <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center justify-between text-sm text-slate-300">
                    <span>Total Belanja</span>
                    <span id="transactionModalTotal" class="text-lg font-semibold text-emerald-300">Rp0</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const moneyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        const state = {
            products: [],
            cart: [],
            receipt: null,
            sortBy: 'name',
            sortDir: 'asc',
            selectedEwallet: null,
            pendingQrisPayload: null,
            checkoutConfirmed: false,
            printAfterCheckout: false,
        };

        const refs = {
            themeToggle: document.getElementById('themeToggle'),
            productSearch: document.getElementById('productSearch'),
            themeIcon: document.getElementById('themeIcon'),
            themeLabel: document.getElementById('themeLabel'),
            barcodeSearch: document.getElementById('barcodeSearch'),
            refreshProducts: document.getElementById('refreshProducts'),
            productsStatus: document.getElementById('productsStatus'),
            productsMeta: document.getElementById('productsMeta'),
            productGrid: document.getElementById('productGrid'),
            productCount: document.getElementById('productCount'),
            sortMenuToggle: document.getElementById('sortMenuToggle'),
            sortMenu: document.getElementById('sortMenu'),
            sortByLabel: document.getElementById('sortByLabel'),
            sortDirBadge: document.getElementById('sortDirBadge'),
            sortStatusLabel: document.getElementById('sortStatusLabel'),
            cartCount: document.getElementById('cartCount'),
            subtotalLabel: document.getElementById('subtotalLabel'),
            totalLabel: document.getElementById('totalLabel'),
            cartEmptyState: document.getElementById('cartEmptyState'),
            cartTableWrapper: document.getElementById('cartTableWrapper'),
            cartTable: document.getElementById('cartTable'),
            clearCart: document.getElementById('clearCart'),
            discountAmount: document.getElementById('discountAmount'),
            discountType: document.getElementById('discountType'),
            paymentMethod: document.getElementById('paymentMethod'),
            cashTendered: document.getElementById('cashTendered'),
            notes: document.getElementById('notes'),
            subtotalValue: document.getElementById('subtotalValue'),
            discountValue: document.getElementById('discountValue'),
            grandTotalValue: document.getElementById('grandTotalValue'),
            changeValue: document.getElementById('changeValue'),
            checkoutButton: document.getElementById('checkoutButton'),
            checkoutStatus: document.getElementById('checkoutStatus'),
            checkoutConfirmModal: document.getElementById('checkoutConfirmModal'),
            checkoutConfirmItems: document.getElementById('checkoutConfirmItems'),
            checkoutConfirmSubtotal: document.getElementById('checkoutConfirmSubtotal'),
            checkoutConfirmDiscount: document.getElementById('checkoutConfirmDiscount'),
            checkoutConfirmTotal: document.getElementById('checkoutConfirmTotal'),
            checkoutConfirmPayment: document.getElementById('checkoutConfirmPayment'),
            confirmCheckoutButton: document.getElementById('confirmCheckoutButton'),
            confirmCheckoutAndPrintButton: document.getElementById('confirmCheckoutAndPrintButton'),
            cancelCheckoutButton: document.getElementById('cancelCheckoutButton'),
            closeCheckoutConfirmModal: document.getElementById('closeCheckoutConfirmModal'),
            ewalletPanel: document.getElementById('ewalletPanel'),
            cashTenderedWrapper: document.getElementById('cashTenderedWrapper'),
            changeRow: document.getElementById('changeRow'),
            qrisModal: document.getElementById('qrisModal'),
            qrisItemsList: document.getElementById('qrisItemsList'),
            qrisSubtotal: document.getElementById('qrisSubtotal'),
            qrisDiscount: document.getElementById('qrisDiscount'),
            qrisAmount: document.getElementById('qrisAmount'),
            qrisTimer: document.getElementById('qrisTimer'),
            checkQrisButton: document.getElementById('checkQrisButton'),
            cancelQrisButton: document.getElementById('cancelQrisButton'),
            transactionsMeta: document.getElementById('transactionsMeta'),
            transactionHistory: document.getElementById('transactionHistory'),
            transactionModal: document.getElementById('transactionModal'),
            transactionModalTitle: document.getElementById('transactionModalTitle'),
            transactionModalDate: document.getElementById('transactionModalDate'),
            transactionModalItems: document.getElementById('transactionModalItems'),
            transactionModalPayment: document.getElementById('transactionModalPayment'),
            transactionModalTotal: document.getElementById('transactionModalTotal'),
            closeTransactionModal: document.getElementById('closeTransactionModal'),
            cardForm: document.getElementById('cardForm'),
            cardHolder: document.getElementById('cardHolder'),
            cardNumber: document.getElementById('cardNumber'),
            cardBank: document.getElementById('cardBank'),
            approvalCode: document.getElementById('approvalCode'),
        };

        const formatMoney = (value) => moneyFormatter.format(Number(value || 0));
        const parseCurrencyInput = (value) => Number(String(value || '').replace(/\D/g, '')) || 0;
        const formatCurrencyInput = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(Number(value || 0))}`;
        const checkoutStatusClasses = {
            info: 'border-cyan-400/30 bg-cyan-400/10 text-cyan-100',
            error: 'border-rose-400/30 bg-rose-400/10 text-rose-100',
            success: 'border-emerald-400/30 bg-emerald-400/10 text-emerald-100',
        };
        const setCheckoutStatus = (message = '', type = 'info') => {
            refs.checkoutStatus.className = `mt-3 rounded-2xl border px-4 py-3 text-sm font-medium ${checkoutStatusClasses[type] || checkoutStatusClasses.info}`;
            refs.checkoutStatus.textContent = message;
            refs.checkoutStatus.classList.toggle('hidden', !message);
        };
        const setCurrencyInputValue = (input, value) => {
            input.value = formatCurrencyInput(value);
        };
        const normalizeCurrencyInput = (input) => {
            const digits = String(input.value || '').replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            const value = Number(digits || 0);

            input.value = digits ? formatCurrencyInput(value) : '';

            return value;
        };
        const formatDateTime = (value) => {
            if (!value) {
                return '-';
            }

            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            }).format(new Date(value));
        };
        const getDiscount = () => {
            const raw = Math.max(0, parseCurrencyInput(refs.discountAmount.value));
            if (refs.discountType.value === 'percent') {
                const pct = Math.min(100, raw);
                return Math.round(calculateSubtotal() * pct / 100);
            }
            return raw;
        };
        const getCashTendered = () => Math.max(0, parseCurrencyInput(refs.cashTendered.value));
        const calculateSubtotal = () => state.cart.reduce((total, item) => total + (item.quantity * item.selling_price), 0);
        const getAppliedDiscount = () => Math.min(getDiscount(), calculateSubtotal());
        const isDiscountTooHigh = () => calculateSubtotal() > 0 && getDiscount() >= calculateSubtotal();
        const calculateGrandTotal = () => Math.max(0, calculateSubtotal() - getAppliedDiscount());
        const getPaymentMethodLabel = () => {
            const method = refs.paymentMethod.value;
            if (method === 'cash') return 'Cash';
            if (method === 'card') return 'Kartu';
            if (method === 'e_wallet') return state.selectedEwallet ? `E-wallet (${state.selectedEwallet.name})` : 'E-wallet';
            if (method === 'bank_transfer') return 'Transfer Bank';
            if (method === 'qris') return 'QRIS';
            return 'Lainnya';
        };
        const renderCheckoutConfirmDetails = () => {
            const subtotal = calculateSubtotal();
            const discount = Math.min(getDiscount(), subtotal);
            const grandTotal = Math.max(0, subtotal - discount);

            refs.checkoutConfirmItems.innerHTML = state.cart.map((item) => `
                <div class="grid gap-2 rounded-3xl border border-white/10 bg-slate-950/70 p-4 text-sm text-slate-200 md:grid-cols-[1fr_auto]">
                    <div>
                        <div class="font-semibold text-white">${item.name}</div>
                        <div class="mt-1 text-xs text-slate-400">${item.quantity} × ${formatMoney(item.selling_price)}</div>
                    </div>
                    <div class="text-right font-semibold text-emerald-300">${formatMoney(item.quantity * item.selling_price)}</div>
                </div>
            `).join('');

            refs.checkoutConfirmSubtotal.textContent = formatMoney(subtotal);
            refs.checkoutConfirmDiscount.textContent = formatMoney(discount);
            refs.checkoutConfirmTotal.textContent = formatMoney(grandTotal);
            refs.checkoutConfirmPayment.textContent = getPaymentMethodLabel();
        };
        const openCheckoutConfirmModal = () => {
            renderCheckoutConfirmDetails();
            refs.checkoutConfirmModal.classList.remove('hidden');
            refs.checkoutConfirmModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };
        const closeCheckoutConfirmModal = () => {
            refs.checkoutConfirmModal.classList.add('hidden');
            refs.checkoutConfirmModal.classList.remove('flex');
            document.body.style.overflow = '';
        };
        const handleCheckoutClick = (event) => {
            if (state.checkoutConfirmed) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            openCheckoutConfirmModal();
        };
        const confirmCheckout = async (showPrint = false) => {
            state.checkoutConfirmed = true;
            state.printAfterCheckout = showPrint;
            closeCheckoutConfirmModal();

            try {
                await checkout();
            } finally {
                state.checkoutConfirmed = false;
            }
        };
        const getStockBadge = (product) => {
            const stock = getRemainingStock(product);
            const minStock = Number(product.min_stock || 0);

            if (stock <= 0) {
                return {
                    label: 'Stok habis',
                    className: 'border-rose-400/40 bg-rose-400/10 text-rose-300',
                };
            }

            if (stock <= minStock) {
                return {
                    label: `Stok menipis: ${stock}`,
                    className: 'border-amber-400/40 bg-amber-400/10 text-amber-300',
                };
            }

            return {
                label: `Stok aman: ${stock}`,
                className: 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300',
            };
        };
        const themeStorageKey = 'erp-pos-theme';
        const themeIcons = {
            sun: `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path>
                    <path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path>
                    <path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path>
                    <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
            `,
            moon: `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="M12 3a6 6 0 0 0 9 7.5A9 9 0 1 1 12 3Z"></path>
                </svg>
            `,
        };

        const applyTheme = (theme) => {
            document.documentElement.dataset.theme = theme;
            refs.themeIcon.innerHTML = theme === 'light' ? themeIcons.moon : themeIcons.sun;
            refs.themeLabel.textContent = theme === 'light' ? 'Mode gelap' : 'Mode terang';
            refs.themeToggle.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');

            try {
                localStorage.setItem(themeStorageKey, theme);
            } catch (error) {
                console.error(error);
            }
        };

        const calculateChange = () => {
            if (refs.paymentMethod.value !== 'cash') {
                return 0;
            }

            return Math.max(0, getCashTendered() - calculateGrandTotal());
        };

        const fetchJson = async (url, options = {}) => {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(options.headers || {}),
                },
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const validationMessage = payload?.errors ? Object.values(payload.errors).flat()[0] : null;
                throw new Error(validationMessage || payload?.message || response.statusText || 'Request failed');
            }

            return payload;
        };

        const updateSummary = () => {
            const subtotal = calculateSubtotal();
            const discount = Math.min(getDiscount(), subtotal);
            const grandTotal = Math.max(0, subtotal - discount);
            const change = calculateChange();
            const paymentMethod = refs.paymentMethod.value;
            const isCashPayment = paymentMethod === 'cash';
            const isEwallet = paymentMethod === 'e_wallet';
            const isInvalidDiscount = isDiscountTooHigh();
            const isCashInsufficient = isCashPayment && state.cart.length > 0 && getCashTendered() < grandTotal;
            const isEwalletNotSelected = isEwallet && !state.selectedEwallet;

            refs.productCount.textContent = String(state.products.length);
            refs.cartCount.textContent = String(state.cart.reduce((total, item) => total + item.quantity, 0));
            refs.subtotalLabel.textContent = formatMoney(subtotal);
            refs.totalLabel.textContent = formatMoney(grandTotal);
            refs.subtotalValue.textContent = formatMoney(subtotal);
            // Show discount as "Rp X (Y%)" 
            if (refs.discountType.value === 'percent') {
                const pct = Math.min(100, Math.max(0, parseCurrencyInput(refs.discountAmount.value)));
                refs.discountValue.textContent = pct > 0
                    ? `${formatMoney(discount)} (${pct}%)`
                    : formatMoney(0);
            } else {
                refs.discountValue.textContent = formatMoney(discount);
            }

            refs.grandTotalValue.textContent = formatMoney(grandTotal);
            refs.changeValue.textContent = formatMoney(change);
            refs.ewalletPanel.classList.toggle('hidden', !isEwallet);
            refs.cardForm.classList.toggle('hidden', paymentMethod !== 'card');
            refs.cashTenderedWrapper.style.display = isCashPayment ? '' : 'none';
            refs.changeRow.style.display = isCashPayment ? '' : 'none';
            refs.checkoutButton.disabled = state.cart.length === 0 || isInvalidDiscount || isCashInsufficient || isEwalletNotSelected;

            if (state.cart.length === 0) {
                setCheckoutStatus('Tambahkan produk ke keranjang terlebih dahulu.', 'info');
                return;
            }

            if (isInvalidDiscount) {
                setCheckoutStatus('Diskon harus lebih kecil dari subtotal.', 'error');
                return;
            }

            if (isCashInsufficient) {
                setCheckoutStatus('Uang dibayar belum cukup.', 'error');
                return;
            }

            if (isEwalletNotSelected) {
                setCheckoutStatus('Pilih e-wallet terlebih dahulu.', 'error');
                return;
            }

            setCheckoutStatus();
        };

        const findCartItem = (productId) => state.cart.find((item) => item.id === productId);
        const getCartQuantity = (productId) => findCartItem(productId)?.quantity || 0;
        const getRemainingStock = (product) => Math.max(0, Number(product.stock_quantity || 0) - getCartQuantity(product.id));

        const addToCart = (product) => {
            if (getRemainingStock(product) <= 0) {
                refs.productsStatus.textContent = `${product.name} sudah mencapai batas stok.`;
                return;
            }

            const existingItem = findCartItem(product.id);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                state.cart.push({
                    ...product,
                    quantity: 1,
                });
            }

            refs.productsStatus.textContent = `${product.name} ditambahkan ke keranjang.`;
            renderCart();
            renderProducts();
        };

        const removeFromCart = (productId) => {
            state.cart = state.cart.filter((item) => item.id !== productId);
            renderCart();
            renderProducts();
        };

        const setQuantity = (productId, quantity) => {
            const item = findCartItem(productId);

            if (!item) {
                return;
            }

            const stock = Number(item.stock_quantity || 0);
            const nextQuantity = Math.max(1, Math.min(stock, Number(quantity || 1)));

            if (nextQuantity !== Number(quantity || 1)) {
                setCheckoutStatus(`Qty ${item.name} disesuaikan dengan stok tersedia.`, 'info');
            }

            item.quantity = nextQuantity;
            renderCart();
            renderProducts();
        };

        const changeQuantity = (productId, delta) => {
            const item = findCartItem(productId);

            if (!item) {
                return;
            }

            if (delta > 0 && item.quantity >= Number(item.stock_quantity || 0)) {
                setCheckoutStatus('Jumlah item sudah mencapai stok tersedia.', 'info');
                return;
            }

            item.quantity += delta;

            if (item.quantity <= 0) {
                removeFromCart(productId);
                return;
            }

            renderCart();
            renderProducts();
        };

        const renderProducts = () => {
            refs.productGrid.innerHTML = '';

            if (state.products.length === 0) {
                refs.productGrid.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 px-4 py-10 text-center text-sm text-slate-400 md:col-span-2 xl:col-span-3">
                        Produk tidak ditemukan. Coba kata kunci lain atau muat ulang daftar produk.
                    </div>
                `;
                refs.productsMeta.textContent = '0 item';
                return;
            }

            refs.productsMeta.textContent = `${state.products.length} item`;

            refs.productGrid.innerHTML = state.products.map((product) => {
                const stockBadge = getStockBadge(product);

                return `
                    <button type="button" data-product-id="${product.id}" class="group flex min-h-44 flex-col justify-between rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-left transition hover:-translate-y-1 hover:border-cyan-400/50 hover:bg-slate-900/90 hover:shadow-xl hover:shadow-cyan-950/30">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold text-cyan-200">${product.sku || '-'}</span>
                            </div>
                            <h3 class="mt-4 line-clamp-2 text-lg font-semibold leading-snug text-white">${product.name}</h3>
                            <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-slate-400">${product.description || 'Tanpa deskripsi'}</p>
                        </div>
                        <div class="mt-5 space-y-3 border-t border-white/10 pt-4">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-lg font-semibold text-emerald-300">${formatMoney(product.selling_price)}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1.5 font-medium text-slate-300 transition group-hover:bg-cyan-400/20 group-hover:text-cyan-100">Tambah</span>
                            </div>
                            <div>
                                <span class="block w-full rounded-xl border px-3 py-2 text-center text-xs font-semibold ${stockBadge.className}">${stockBadge.label}</span>
                            </div>
                        </div>
                    </button>
                `;
            }).join('');

            refs.productGrid.querySelectorAll('[data-product-id]').forEach((button) => {
                button.addEventListener('click', () => {
                    const product = state.products.find((item) => String(item.id) === button.dataset.productId);

                    if (product) {
                        addToCart(product);
                    }
                });
            });
        };

        const renderCart = () => {
            refs.cartTable.innerHTML = '';

            if (state.cart.length === 0) {
                refs.cartEmptyState.classList.remove('hidden');
                refs.cartTableWrapper.classList.add('hidden');
                updateSummary();
                return;
            }

            refs.cartEmptyState.classList.add('hidden');
            refs.cartTableWrapper.classList.remove('hidden');
            refs.cartTable.innerHTML = state.cart.map((item) => `
                <tr>
                    <td class="px-3 py-3 align-top">
                        <div class="font-medium text-white">${item.name}</div>
                        <div class="text-xs text-slate-400">${formatMoney(item.selling_price)} / pcs</div>
                    </td>
                    <td class="px-3 py-3 align-top">
                        <div class="inline-flex items-center rounded-full border border-white/10 bg-slate-950/70">
                            <button type="button" data-decrease="${item.id}" class="px-2 py-1 text-slate-300 hover:text-white">-</button>
                            <input type="number" min="1" max="${item.stock_quantity}" value="${item.quantity}" data-quantity="${item.id}" class="w-14 border-x border-white/10 bg-transparent px-2 py-1 text-center text-white outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                            <button type="button" data-increase="${item.id}" class="px-2 py-1 text-slate-300 hover:text-white">+</button>
                        </div>
                    </td>
                    <td class="px-3 py-3 align-top font-semibold text-emerald-300">${formatMoney(item.quantity * item.selling_price)}</td>
                    <td class="px-3 py-3 align-top text-right">
                        <button type="button" data-remove="${item.id}" class="text-xs text-rose-300 hover:text-rose-200">Hapus</button>
                    </td>
                </tr>
            `).join('');

            refs.cartTable.querySelectorAll('[data-decrease]').forEach((button) => {
                button.addEventListener('click', () => changeQuantity(Number(button.dataset.decrease), -1));
            });

            refs.cartTable.querySelectorAll('[data-increase]').forEach((button) => {
                button.addEventListener('click', () => changeQuantity(Number(button.dataset.increase), 1));
            });

            refs.cartTable.querySelectorAll('[data-quantity]').forEach((input) => {
                input.addEventListener('change', () => setQuantity(Number(input.dataset.quantity), input.value));
                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        setQuantity(Number(input.dataset.quantity), input.value);
                        input.blur();
                    }
                });
            });

            refs.cartTable.querySelectorAll('[data-remove]').forEach((button) => {
                button.addEventListener('click', () => removeFromCart(Number(button.dataset.remove)));
            });

            updateSummary();
        };

        const loadProducts = async (search = '') => {
            refs.productsStatus.textContent = 'Memuat produk...';

            try {
                const params = new URLSearchParams();
                if (search) params.set('search', search);
                params.set('sort_by', state.sortBy);
                params.set('sort_dir', state.sortDir);

                const response = await fetchJson(`/products/sort?${params.toString()}`);
                state.products = response.data ?? [];
                renderProducts();
                updateSummary();
                refs.productsStatus.textContent = search
                    ? `Menampilkan hasil untuk "${search}".`
                    : 'Pilih produk untuk dimasukkan ke keranjang.';
            } catch (error) {
                refs.productsStatus.textContent = error.message || 'Gagal memuat produk.';
                refs.productGrid.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-rose-400/30 bg-rose-400/10 px-4 py-10 text-center text-sm text-rose-200 md:col-span-2 xl:col-span-3">
                        ${error.message || 'Terjadi kesalahan saat mengambil produk.'}
                    </div>
                `;
            }
        };

        const openTransactionModal = (transaction) => {
            const details = transaction.details ?? [];
            const payments = transaction.payments ?? [];
            const transactionCode = `TRX-${String(transaction.id).padStart(4, '0')}`;

            refs.transactionModalTitle.textContent = transactionCode;
            refs.transactionModalDate.textContent = formatDateTime(transaction.created_at);
            refs.transactionModalTotal.textContent = formatMoney(transaction.total);
            refs.transactionModalPayment.innerHTML = payments.length ? payments.map((payment) => `
                <div>Metode: <span class="font-semibold text-white">${payment.payment_method}</span></div>
                <div>Cash: <span class="font-semibold text-white">${formatMoney(payment.cash_tendered)}</span></div>
                <div>Diskon: <span class="font-semibold text-white">${formatMoney(payment.discount_amount)}</span></div>
                <div>Kembalian: <span class="font-semibold text-white">${formatMoney(payment.change_amount)}</span></div>
            `).join('') : `
                <div>Metode: <span class="font-semibold text-white">cash</span></div>
                <div>Cash: <span class="font-semibold text-white">${formatMoney(0)}</span></div>
                <div>Diskon: <span class="font-semibold text-white">${formatMoney(0)}</span></div>
                <div>Kembalian: <span class="font-semibold text-white">${formatMoney(0)}</span></div>
            `;
            refs.transactionModalItems.innerHTML = details.length ? details.map((item) => `
                <div class="flex items-center justify-between gap-4 px-4 py-3 text-sm text-slate-300">
                    <div>
                        <div class="font-medium text-white">${item.product?.name || `Produk #${item.product_id}`}</div>
                        <div class="text-xs text-slate-400">${item.quantity} x ${formatMoney(item.price)}</div>
                    </div>
                    <div class="font-semibold text-emerald-300">${formatMoney(item.amount)}</div>
                </div>
            `).join('') : `
                <div class="px-4 py-4 text-sm text-slate-400">Detail item tidak tersedia.</div>
            `;

            refs.transactionModal.classList.remove('hidden');
            refs.transactionModal.classList.add('flex');
        };

        const closeTransactionModal = () => {
            refs.transactionModal.classList.add('hidden');
            refs.transactionModal.classList.remove('flex');
        };

        const renderTransactionHistory = (transactions) => {
            refs.transactionsMeta.textContent = transactions.length > 5 ? `5 terbaru dari ${transactions.length} transaksi` : `${transactions.length} transaksi`;

            if (!transactions.length) {
                refs.transactionHistory.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/60 p-4 text-sm text-slate-400">
                        Belum ada transaksi.
                    </div>
                `;
                return;
            }

            refs.transactionHistory.innerHTML = transactions.slice(0, 5).map((transaction) => {
                const details = transaction.details ?? [];
                const itemCount = details.reduce((total, item) => total + Number(item.quantity || 0), 0);
                const transactionCode = `TRX-${String(transaction.id).padStart(4, '0')}`;

                return `
                    <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm">
                        <button type="button" data-show-transaction="${transaction.id}" class="flex w-full items-start justify-between gap-3 text-left">
                            <div>
                                <div class="font-semibold text-white">${transactionCode}</div>
                                <div class="mt-1 text-xs text-slate-400">${formatDateTime(transaction.created_at)}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-emerald-300">${formatMoney(transaction.total)}</div>
                                <div class="mt-1 text-xs text-slate-400">${itemCount} item</div>
                            </div>
                        </button>
                    </div>
                `;
            }).join('');

            refs.transactionHistory.querySelectorAll('[data-show-transaction]').forEach((button) => {
                button.addEventListener('click', () => {
                    const transaction = transactions.find((item) => String(item.id) === button.dataset.showTransaction);

                    if (transaction) {
                        openTransactionModal(transaction);
                    }
                });
            });
        };

        const checkout = async () => {
            if (state.cart.length === 0) {
                setCheckoutStatus('Keranjang masih kosong.', 'info');
                return;
            }
            if (isDiscountTooHigh()) {
                setCheckoutStatus('Diskon harus lebih kecil dari subtotal.', 'error');
                return;
            }
            if (refs.paymentMethod.value === 'cash' && getCashTendered() < calculateGrandTotal()) {
                setCheckoutStatus('Uang dibayar belum cukup.', 'error');
                return;
            }

            if (refs.paymentMethod.value === 'qris') {
                state.pendingQrisPayload = {
                    items: state.cart.map((item) => ({
                        product_id: item.id,
                        quantity: item.quantity,
                        unit_price: item.selling_price,
                    })),
                    discount_amount: getAppliedDiscount(),
                    payment_method: 'qris',
                    cash_tendered: 0,
                    notes: refs.notes.value,
                };
                const subtotal = calculateSubtotal();
                const discount = Math.min(getDiscount(), subtotal);
                refs.qrisSubtotal.textContent = formatMoney(subtotal);
                refs.qrisDiscount.textContent = formatMoney(discount);
                refs.qrisAmount.textContent = formatMoney(Math.max(0, subtotal - discount));
                refs.qrisItemsList.innerHTML = state.cart.map((item) => `
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <div>
                            <div class="font-medium text-white">${item.name}</div>
                            <div class="text-xs text-slate-400">${item.quantity} × ${formatMoney(item.selling_price)}</div>
                        </div>
                        <div class="font-semibold text-emerald-300">${formatMoney(item.quantity * item.selling_price)}</div>
                    </div>
                `).join('');
                openQrisModal();
                return;
            }

            const payload = {
                customer_id: selectedCustomer,
                items: state.cart.map((item) => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.selling_price,
                })),
                discount_amount: getAppliedDiscount(),
                payment_method: refs.paymentMethod.value,
                cash_tendered: refs.paymentMethod.value === 'cash' ? getCashTendered() : 0,
                notes: refs.paymentMethod.value === 'e_wallet' && state.selectedEwallet
                    ? `[${state.selectedEwallet.name}]${refs.notes.value ? ' - ' + refs.notes.value : ''}`
                    : refs.notes.value,
                card_info: refs.paymentMethod.value === 'card'
                    ? {
                        card_holder: refs.cardHolder.value,
                        card_number_last4: refs.cardNumber.value,
                        bank: refs.cardBank.value,
                        approval_code: refs.approvalCode.value,
                    }
                    : null,
            };

            refs.checkoutButton.disabled = true;
            setCheckoutStatus('Menyimpan transaksi...', 'info');

            try {
                const response = await fetchJson('/pos/checkout', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });

                state.receipt = response.data;
                state.cart = [];
                selectedCustomer = null;

                document.getElementById("memberSearch").value = "";
                document.getElementById("memberResult").innerHTML = "";
                state.selectedEwallet = null;
                document.querySelectorAll('.ewallet-btn').forEach((b) => {
                    b.classList.remove('border-cyan-400/70', 'bg-cyan-400/10');
                    b.classList.add('border-white/10', 'bg-slate-950/70');
                    b.querySelector('.ewallet-check').classList.add('hidden');
                });
                setCurrencyInputValue(refs.discountAmount, 0);
                setCurrencyInputValue(refs.cashTendered, 0);
                refs.notes.value = '';
                renderCart();
                await loadProducts(refs.productSearch.value.trim());
                await loadTransactions();
                setCheckoutStatus('Transaksi berhasil disimpan.', 'success');
                if (state.printAfterCheckout) {
                    window.open('/transactions/' + response.data.id + '/receipt', '_blank');
                }
            } catch (error) {
                setCheckoutStatus(error.message || 'Checkout gagal.', 'error');
            } finally {
                refs.checkoutButton.disabled = state.cart.length === 0;
            }
        };

        const loadTransactions = async () => {
            try {
                const response = await fetchJson('/transactions');
                const transactions = response.data ?? [];
                renderTransactionHistory(transactions);

            } catch (error) {
                console.error(error);
            }
        };

        let qrisTimerInterval = null;

        const openQrisModal = () => {
            refs.qrisModal.classList.remove('hidden');
            refs.qrisModal.classList.add('flex');
            let remaining = 60;
            refs.qrisTimer.textContent = '01:00';
            refs.qrisTimer.classList.remove('text-rose-400');
            refs.qrisTimer.classList.add('text-white');
            clearInterval(qrisTimerInterval);
            qrisTimerInterval = setInterval(() => {
                remaining--;
                const m = Math.floor(remaining / 60);
                const s = remaining % 60;
                refs.qrisTimer.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                if (remaining <= 10) {
                    refs.qrisTimer.classList.remove('text-white');
                    refs.qrisTimer.classList.add('text-rose-400');
                }
                if (remaining <= 0) {
                    closeQrisModal();
                    setCheckoutStatus('Waktu pembayaran QRIS habis. Silakan coba lagi.', 'error');
                }
            }, 1000);
        };

        const closeQrisModal = () => {
            refs.qrisModal.classList.add('hidden');
            refs.qrisModal.classList.remove('flex');
            clearInterval(qrisTimerInterval);
            refs.qrisTimer.textContent = '01:00';
            refs.qrisTimer.classList.remove('text-rose-400');
            refs.qrisTimer.classList.add('text-white');
            state.pendingQrisPayload = null;
        };

        refs.checkQrisButton.addEventListener('click', async () => {
            if (!state.pendingQrisPayload) return;
            const originalHTML = refs.checkQrisButton.innerHTML;
            refs.checkQrisButton.disabled = true;
            refs.checkQrisButton.textContent = 'Memeriksa...';
            try {
                const response = await fetchJson('/pos/checkout', {
                    method: 'POST',
                    body: JSON.stringify(state.pendingQrisPayload),
                });
                closeQrisModal();
                state.receipt = response.data;
                state.cart = [];
                setCurrencyInputValue(refs.discountAmount, 0);
                setCurrencyInputValue(refs.cashTendered, 0);
                refs.notes.value = '';
                renderCart();
                await loadProducts(refs.productSearch.value.trim());
                await loadTransactions();
                setCheckoutStatus('Transaksi QRIS berhasil disimpan.', 'success');
                if (state.printAfterCheckout) {
                    window.open('/transactions/' + response.data.id + '/receipt', '_blank');
                }
            } catch (error) {
                setCheckoutStatus(error.message || 'Checkout gagal.', 'error');
                closeQrisModal();
            } finally {
                refs.checkQrisButton.disabled = false;
                refs.checkQrisButton.innerHTML = originalHTML;
            }
        });

        refs.cancelQrisButton.addEventListener('click', closeQrisModal);
        refs.confirmCheckoutButton.addEventListener('click', () => confirmCheckout(false));
        refs.confirmCheckoutAndPrintButton.addEventListener('click', () => confirmCheckout(true));
        refs.cancelCheckoutButton.addEventListener('click', closeCheckoutConfirmModal);
        refs.closeCheckoutConfirmModal.addEventListener('click', closeCheckoutConfirmModal);
        refs.checkoutButton.addEventListener('click', handleCheckoutClick, true);

        let searchTimer = null;

        refs.productSearch.addEventListener('input', () => {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(() => {
                loadProducts(refs.productSearch.value.trim());
            }, 250);
        });

        refs.barcodeSearch.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();
            const keyword = refs.barcodeSearch.value.trim();

            if (!keyword) {
                return;
            }

            try {
                const response = await fetchJson(`/products?search=${encodeURIComponent(keyword)}`);
                const [product] = response.data ?? [];

                if (!product) {
                    refs.productsStatus.textContent = 'Produk tidak ditemukan.';
                    return;
                }

                addToCart(product);
                refs.barcodeSearch.value = '';
                refs.productsStatus.textContent = `${product.name} berhasil ditambahkan.`;
            } catch (error) {
                refs.productsStatus.textContent = error.message || 'Pencarian barcode gagal.';
            }
        });

        refs.refreshProducts.addEventListener('click', () => loadProducts(refs.productSearch.value.trim()));

        // ---- Sort menu ----
        const sortLabels = {
            'name:asc':            { by: 'Nama',  dir: 'A→Z',        status: 'Sorting berdasarkan nama (A → Z)' },
            'name:desc':           { by: 'Nama',  dir: 'Z→A',        status: 'Sorting berdasarkan nama (Z → A)' },
            'selling_price:asc':   { by: 'Harga', dir: 'Murah→Mahal', status: 'Sorting berdasarkan harga (Termurah → Termahal)' },
            'selling_price:desc':  { by: 'Harga', dir: 'Mahal→Murah', status: 'Sorting berdasarkan harga (Termahal → Termurah)' },
        };

        const applySortUI = () => {
            const key = `${state.sortBy}:${state.sortDir}`;
            const label = sortLabels[key] || sortLabels['name:asc'];
            refs.sortByLabel.textContent = label.by;
            refs.sortDirBadge.textContent = label.dir;
            refs.sortStatusLabel.textContent = label.status;
            refs.sortStatusLabel.classList.remove('hidden');

            // Highlight active option
            refs.sortMenu.querySelectorAll('.sort-option').forEach((btn) => {
                const isActive = btn.dataset.sortBy === state.sortBy && btn.dataset.sortDir === state.sortDir;
                btn.classList.toggle('text-cyan-300', isActive);
                btn.classList.toggle('bg-cyan-400/10', isActive);
                btn.classList.toggle('text-slate-300', !isActive);
            });
        };

        refs.sortMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            refs.sortMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', () => refs.sortMenu.classList.add('hidden'));
        refs.sortMenu.addEventListener('click', (e) => e.stopPropagation());

        refs.sortMenu.querySelectorAll('.sort-option').forEach((btn) => {
            btn.addEventListener('click', () => {
                state.sortBy  = btn.dataset.sortBy;
                state.sortDir = btn.dataset.sortDir;
                applySortUI();
                refs.sortMenu.classList.add('hidden');
                loadProducts(refs.productSearch.value.trim());
            });
        });
        refs.clearCart.addEventListener('click', () => {
            state.cart = [];
            renderCart();
            renderProducts();
        });
        [refs.discountAmount, refs.cashTendered].forEach((input) => {
            input.addEventListener('input', () => {
                // Skip currency normalization for discount when in percent mode
                if (input === refs.discountAmount && refs.discountType.value === 'percent') {
                    // Allow only digits, clamp to 0-100
                    const digits = input.value.replace(/\D/g, '');
                    const pct = Math.min(100, Number(digits || 0));
                    input.value = digits ? String(pct) : '';
                    updateSummary();
                    return;
                }
                normalizeCurrencyInput(input);
                updateSummary();
            });
            input.addEventListener('blur', () => {
                if (input === refs.discountAmount && refs.discountType.value === 'percent') {
                    // Clamp and clean up on blur in percent mode
                    const digits = input.value.replace(/\D/g, '');
                    const pct = Math.min(100, Number(digits || 0));
                    input.value = pct > 0 ? String(pct) : '';
                    updateSummary();
                    return;
                }
                if (!input.value) {
                    setCurrencyInputValue(input, 0);
                    updateSummary();
                }
            });
        });

        // Handle discount type toggle (Rp / %)
        const syncDiscountInputMode = () => {
            const isPercent = refs.discountType.value === 'percent';
            // Extract raw numeric value before switching mode
            const currentRaw = parseCurrencyInput(refs.discountAmount.value);

            if (isPercent) {
                // Clamp to 0-100 and display as plain number
                const pct = Math.min(100, currentRaw);
                refs.discountAmount.value = pct > 0 ? String(pct) : '';
                refs.discountAmount.placeholder = '0 – 100';
                refs.discountAmount.inputMode = 'numeric';
            } else {
                // Switch back to currency format
                setCurrencyInputValue(refs.discountAmount, currentRaw);
                refs.discountAmount.placeholder = '';
                refs.discountAmount.inputMode = 'numeric';
            }
            updateSummary();
        };

        refs.discountType.addEventListener('change', syncDiscountInputMode);

        refs.paymentMethod.addEventListener('change', () => {
            state.selectedEwallet = null;
            document.querySelectorAll('.ewallet-btn').forEach((b) => {
                b.classList.remove('border-cyan-400/70', 'bg-cyan-400/10');
                b.classList.add('border-white/10', 'bg-slate-950/70');
                b.querySelector('.ewallet-check').classList.add('hidden');
            });
            updateSummary();
        });
        document.querySelectorAll('.ewallet-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.ewallet-btn').forEach((b) => {
                    b.classList.remove('border-cyan-400/70', 'bg-cyan-400/10');
                    b.classList.add('border-white/10', 'bg-slate-950/70');
                    b.querySelector('.ewallet-check').classList.add('hidden');
                });
                btn.classList.remove('border-white/10', 'bg-slate-950/70');
                btn.classList.add('border-cyan-400/70', 'bg-cyan-400/10');
                btn.querySelector('.ewallet-check').classList.remove('hidden');
                state.selectedEwallet = { id: btn.dataset.ewallet, name: btn.dataset.name };
                updateSummary();
            });
        });
        refs.checkoutButton.addEventListener('click', checkout);
        refs.closeTransactionModal.addEventListener('click', closeTransactionModal);
        refs.transactionModal.addEventListener('click', (event) => {
            if (event.target === refs.transactionModal) {
                closeTransactionModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeTransactionModal();
            }
        });
        refs.themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.dataset.theme === 'light' ? 'light' : 'dark';
            applyTheme(currentTheme === 'light' ? 'dark' : 'light');
        });

        const sunIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`;
        const moonIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 7.5A9 9 0 1 1 12 3Z"/></svg>`;

        const updateClock = () => {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const h = now.getHours();
            const m = now.getMinutes();
            const totalMinutes = h * 60 + m;
            const isSunTime = totalMinutes >= 330 && totalMinutes < 1080; // 05:30 - 17:59

            document.getElementById('clockDate').textContent =
                `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            document.getElementById('clockTime').textContent =
                String(h).padStart(2,'0') + ':' +
                String(m).padStart(2,'0') + ':' +
                String(now.getSeconds()).padStart(2,'0');
            document.getElementById('clockIcon').innerHTML = isSunTime ? sunIconSvg : moonIconSvg;
        };
        updateClock();
        setInterval(updateClock, 1000);

        applyTheme(document.documentElement.dataset.theme === 'light' ? 'light' : 'dark');
        applySortUI();
        setCurrencyInputValue(refs.discountAmount, getDiscount());
        setCurrencyInputValue(refs.cashTendered, getCashTendered());
        loadProducts();
        loadTransactions();

        let selectedCustomer = null;

        document
        .getElementById("memberSearch")
        .addEventListener("keyup", async function () {

            const keyword = this.value.trim();

            if (keyword.length < 4) {

                document.getElementById("memberResult").innerHTML = "";

                return;

            }

            try{

                const response = await fetch(
                    "/customers/search?keyword=" + encodeURIComponent(keyword)
                );

                const result = await response.json();

                let html = "";

                result.data.forEach(customer => {

                    html += `
                    <div
                        onclick="chooseMember(${customer.id},'${customer.name}','${customer.phone}')"
                        class="cursor-pointer rounded-xl border border-cyan-500/30 bg-slate-900 p-3 hover:bg-cyan-700/30 transition">

                        <div class="font-semibold text-white">
                            ${customer.name}
                        </div>

                        <div class="text-sm text-slate-300">
                            ${customer.phone}
                        </div>

                        <div class="text-xs text-cyan-300">
                            ${customer.member_level} • ${customer.points} poin
                        </div>

                    </div>
                    `;

                });

                if(result.data.length===0){

                    html = `
                        <div class="rounded-lg bg-slate-900 p-3 text-slate-400">
                            Member tidak ditemukan
                        </div>
                    `;

                }

                document.getElementById("memberResult").innerHTML = html;

            }catch(error){

                console.error(error);

            }

        });

        function chooseMember(id,name,phone){

            selectedCustomer=id;

            document.getElementById("memberSearch").value =
                name+" ("+phone+")";

            document.getElementById("memberResult").innerHTML=`
                <div class="rounded-xl border border-green-500 bg-green-500/20 p-3 text-green-300">
                    ✓ ${name}
                </div>
            `;

        }

        const showPrintReceiptButton = (transactionId) => {
            const existing = document.getElementById('printReceiptBtn');
            if (existing) existing.remove();
            const btn = document.createElement('a');
            btn.id = 'printReceiptBtn';
            btn.href = '/transactions/' + transactionId + '/receipt';
            btn.target = '_blank';
            btn.className = 'mt-3 flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 font-medium text-emerald-200 transition hover:bg-emerald-400/20';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg> Cetak Struk';
            refs.checkoutButton.after(btn);
        };
    </script>
</body>
</html>
