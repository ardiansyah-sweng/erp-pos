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
        html[data-theme="light"] .bg-slate-900\/90 {
            background-color: #f8fafc !important;
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

        html[data-theme="light"] .text-slate-200 {
            color: #334155 !important;
        }

        html[data-theme="light"] .text-rose-300 {
            color: #e11d48 !important;
        }

        html[data-theme="light"] .text-cyan-300\/80,
        html[data-theme="light"] .text-cyan-300\/70,
        html[data-theme="light"] .text-cyan-200 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] .text-emerald-300 {
            color: #047857 !important;
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
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Point of Sales</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Kasir cepat untuk transaksi harian.</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Cari atau scan produk, cek isi keranjang, lalu selesaikan pembayaran tanpa reload halaman.</p>
                    <button id="themeToggle" type="button" class="mt-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white" aria-pressed="false">
                        <span id="themeIcon" aria-hidden="true" class="inline-flex h-4 w-4"></span>
                        <span id="themeLabel">Mode terang</span>
                    </button>
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
                            <select id="sortBy" class="rounded-xl border border-white/10 bg-slate-950/70 px-3 py-1.5 text-sm text-white outline-none focus:border-cyan-400">
                                <option value="name">Nama</option>
                                <option value="selling_price">Harga</option>
                            </select>
                            <button id="sortDirToggle" title="Urutan" class="flex items-center gap-1 rounded-xl border border-white/10 bg-slate-950/70 px-3 py-1.5 text-sm text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                                <span id="sortDirLabel">A→Z</span>
                            </button>
                        </div>
                    </div>
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
                    <div class="overflow-hidden rounded-2xl border border-white/10">
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
                            <input id="discountAmount" type="number" min="0" value="0" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="paymentMethod">Metode pembayaran</label>
                            <select id="paymentMethod" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-cyan-400">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="e_wallet">E-wallet</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="cashTendered">Uang dibayar</label>
                            <input id="cashTendered" type="number" min="0" value="0" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400">
                        </div>
                        <div>
                            <label class="text-sm text-slate-300" for="notes">Catatan</label>
                            <textarea id="notes" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400" placeholder="Opsional"></textarea>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-sm text-slate-300">
                        <div class="flex items-center justify-between"><span>Subtotal</span><span id="subtotalValue" class="font-semibold text-white">Rp0</span></div>
                        <div class="flex items-center justify-between"><span>Diskon</span><span id="discountValue" class="font-semibold text-white">Rp0</span></div>
                        <div class="flex items-center justify-between"><span>Total</span><span id="grandTotalValue" class="font-semibold text-emerald-300">Rp0</span></div>
                        <div class="flex items-center justify-between"><span>Kembalian</span><span id="changeValue" class="font-semibold text-cyan-300">Rp0</span></div>
                    </div>

                    <button id="checkoutButton" class="mt-5 w-full rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50" disabled>Checkout</button>
                    <p id="checkoutStatus" class="mt-3 text-sm text-slate-400"></p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <h2 class="text-lg font-semibold text-white">Struk Terakhir</h2>
                    <div id="receiptBox" class="mt-4 rounded-2xl border border-dashed border-white/10 bg-slate-950/60 p-4 text-sm text-slate-300">
                        Belum ada transaksi.
                    </div>
                </div>
            </aside>
        </section>
    </main>

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
            sortBy: document.getElementById('sortBy'),
            sortDirToggle: document.getElementById('sortDirToggle'),
            sortDirLabel: document.getElementById('sortDirLabel'),
            cartCount: document.getElementById('cartCount'),
            subtotalLabel: document.getElementById('subtotalLabel'),
            totalLabel: document.getElementById('totalLabel'),
            cartEmptyState: document.getElementById('cartEmptyState'),
            cartTable: document.getElementById('cartTable'),
            clearCart: document.getElementById('clearCart'),
            discountAmount: document.getElementById('discountAmount'),
            paymentMethod: document.getElementById('paymentMethod'),
            cashTendered: document.getElementById('cashTendered'),
            notes: document.getElementById('notes'),
            subtotalValue: document.getElementById('subtotalValue'),
            discountValue: document.getElementById('discountValue'),
            grandTotalValue: document.getElementById('grandTotalValue'),
            changeValue: document.getElementById('changeValue'),
            checkoutButton: document.getElementById('checkoutButton'),
            checkoutStatus: document.getElementById('checkoutStatus'),
            receiptBox: document.getElementById('receiptBox'),
        };

        const formatMoney = (value) => moneyFormatter.format(Number(value || 0));
        const getDiscount = () => Math.max(0, Number(refs.discountAmount.value || 0));
        const getCashTendered = () => Math.max(0, Number(refs.cashTendered.value || 0));
        const calculateSubtotal = () => state.cart.reduce((total, item) => total + (item.quantity * item.selling_price), 0);
        const calculateGrandTotal = () => Math.max(0, calculateSubtotal() - getDiscount());
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
            const isCashPayment = refs.paymentMethod.value === 'cash';
            const isCashInsufficient = isCashPayment && state.cart.length > 0 && getCashTendered() < grandTotal;

            if (getDiscount() > subtotal) {
                refs.discountAmount.value = String(subtotal);
            }

            refs.productCount.textContent = String(state.products.length);
            refs.cartCount.textContent = String(state.cart.reduce((total, item) => total + item.quantity, 0));
            refs.subtotalLabel.textContent = formatMoney(subtotal);
            refs.totalLabel.textContent = formatMoney(grandTotal);
            refs.subtotalValue.textContent = formatMoney(subtotal);
            refs.discountValue.textContent = formatMoney(discount);
            refs.grandTotalValue.textContent = formatMoney(grandTotal);
            refs.changeValue.textContent = formatMoney(change);
            refs.checkoutButton.disabled = state.cart.length === 0 || isCashInsufficient;
            refs.checkoutStatus.textContent = state.cart.length === 0
                ? 'Tambahkan produk ke keranjang terlebih dahulu.'
                : isCashInsufficient
                    ? 'Uang dibayar belum cukup.'
                    : '';
        };

        const findCartItem = (productId) => state.cart.find((item) => item.id === productId);

        const addToCart = (product) => {
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
        };

        const removeFromCart = (productId) => {
            state.cart = state.cart.filter((item) => item.id !== productId);
            renderCart();
        };

        const changeQuantity = (productId, delta) => {
            const item = findCartItem(productId);

            if (!item) {
                return;
            }

            item.quantity += delta;

            if (item.quantity <= 0) {
                removeFromCart(productId);
                return;
            }

            renderCart();
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

            refs.productGrid.innerHTML = state.products.map((product) => `
                <button type="button" data-product-id="${product.id}" class="group flex min-h-44 flex-col justify-between rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-left transition hover:-translate-y-1 hover:border-cyan-400/50 hover:bg-slate-900/90 hover:shadow-xl hover:shadow-cyan-950/30">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1 text-xs font-semibold text-cyan-200">${product.sku || '-'}</span>
                            <span class="shrink-0 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">Stok ${product.stock_quantity}</span>
                        </div>
                        <h3 class="mt-4 line-clamp-2 text-lg font-semibold leading-snug text-white">${product.name}</h3>
                        <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-5 text-slate-400">${product.description || 'Tanpa deskripsi'}</p>
                    </div>
                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-white/10 pt-4 text-sm">
                        <span class="text-lg font-semibold text-emerald-300">${formatMoney(product.selling_price)}</span>
                        <span class="rounded-full bg-white/10 px-3 py-1.5 font-medium text-slate-300 transition group-hover:bg-cyan-400/20 group-hover:text-cyan-100">Tambah</span>
                    </div>
                </button>
            `).join('');

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
                updateSummary();
                return;
            }

            refs.cartEmptyState.classList.add('hidden');
            refs.cartTable.innerHTML = state.cart.map((item) => `
                <tr>
                    <td class="px-3 py-3 align-top">
                        <div class="font-medium text-white">${item.name}</div>
                        <div class="text-xs text-slate-400">${formatMoney(item.selling_price)} / pcs</div>
                    </td>
                    <td class="px-3 py-3 align-top">
                        <div class="inline-flex items-center rounded-full border border-white/10 bg-slate-950/70">
                            <button type="button" data-decrease="${item.id}" class="px-2 py-1 text-slate-300 hover:text-white">-</button>
                            <span class="min-w-10 px-3 py-1 text-center">${item.quantity}</span>
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

        const renderReceipt = (receipt) => {
            refs.receiptBox.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">${receipt.transaction_number}</div>
                            <div class="mt-1 text-base font-semibold text-white">${receipt.payment_status}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-slate-400">Total</div>
                            <div class="text-lg font-semibold text-emerald-300">${formatMoney(receipt.total_amount)}</div>
                        </div>
                    </div>
                    <div class="divide-y divide-white/5 rounded-2xl border border-white/10 bg-slate-950/60">
                        ${receipt.details.map((item) => `
                            <div class="flex items-center justify-between px-4 py-3 text-sm text-slate-300">
                                <div>
                                    <div class="font-medium text-white">${item.product.name}</div>
                                    <div class="text-xs text-slate-400">${item.quantity} x ${formatMoney(item.unit_price)}</div>
                                </div>
                                <div class="font-semibold text-emerald-300">${formatMoney(item.total_price)}</div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="grid grid-cols-2 gap-3 rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-300">
                        <div>Metode: <span class="font-semibold text-white">${receipt.payment_method}</span></div>
                        <div>Cash: <span class="font-semibold text-white">${formatMoney(receipt.cash_tendered)}</span></div>
                        <div>Diskon: <span class="font-semibold text-white">${formatMoney(receipt.discount_amount)}</span></div>
                        <div>Kembalian: <span class="font-semibold text-white">${formatMoney(receipt.change_amount)}</span></div>
                    </div>
                </div>
            `;
        };

        const checkout = async () => {
            if (state.cart.length === 0) {
                refs.checkoutStatus.textContent = 'Keranjang masih kosong.';
                return;
            }
            if (refs.paymentMethod.value === 'cash' && getCashTendered() < calculateGrandTotal()) {
                refs.checkoutStatus.textContent = 'Uang dibayar belum cukup.';
                return;
            }

            const payload = {
                items: state.cart.map((item) => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.selling_price,
                })),
                discount_amount: getDiscount(),
                payment_method: refs.paymentMethod.value,
                cash_tendered: refs.paymentMethod.value === 'cash' ? getCashTendered() : 0,
                notes: refs.notes.value,
            };

            refs.checkoutButton.disabled = true;
            refs.checkoutStatus.textContent = 'Menyimpan transaksi...';

            try {
                const response = await fetchJson('/pos/checkout', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });

                state.receipt = response.data;
                renderReceipt(state.receipt);
                state.cart = [];
                refs.discountAmount.value = '0';
                refs.cashTendered.value = '0';
                refs.notes.value = '';
                renderCart();
                await loadProducts(refs.productSearch.value.trim());
                await loadTransactions();
                refs.checkoutStatus.textContent = 'Transaksi berhasil disimpan.';
            } catch (error) {
                refs.checkoutStatus.textContent = error.message || 'Checkout gagal.';
            } finally {
                refs.checkoutButton.disabled = state.cart.length === 0;
            }
        };

        const loadTransactions = async () => {
            try {
                const response = await fetchJson('/transactions');
                const transactions = response.data ?? [];

                if (!transactions.length || state.receipt) {
                    return;
                }

                const latest = transactions[0];
                const itemCount = latest.details?.length ?? 0;

                refs.receiptBox.innerHTML = `
                    <div class="space-y-2">
                        <div class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Transaksi terakhir</div>
                        <div class="text-base font-semibold text-white">Total ${formatMoney(latest.total)}</div>
                        <div class="text-sm text-slate-400">${itemCount} item tersimpan di database.</div>
                    </div>
                `;
            } catch (error) {
                console.error(error);
            }
        };

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

        refs.sortBy.addEventListener('change', () => {
            state.sortBy = refs.sortBy.value;
            // Update label tombol arah sesuai kolom
            refs.sortDirLabel.textContent = state.sortDir === 'asc'
                ? (state.sortBy === 'selling_price' ? 'Murah→Mahal' : 'A→Z')
                : (state.sortBy === 'selling_price' ? 'Mahal→Murah' : 'Z→A');
            loadProducts(refs.productSearch.value.trim());
        });

        refs.sortDirToggle.addEventListener('click', () => {
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            refs.sortDirLabel.textContent = state.sortDir === 'asc'
                ? (state.sortBy === 'selling_price' ? 'Murah→Mahal' : 'A→Z')
                : (state.sortBy === 'selling_price' ? 'Mahal→Murah' : 'Z→A');
            loadProducts(refs.productSearch.value.trim());
        });
        refs.clearCart.addEventListener('click', () => {
            state.cart = [];
            renderCart();
        });
        refs.discountAmount.addEventListener('input', updateSummary);
        refs.cashTendered.addEventListener('input', updateSummary);
        refs.paymentMethod.addEventListener('change', updateSummary);
        refs.checkoutButton.addEventListener('click', checkout);
        refs.themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.dataset.theme === 'light' ? 'light' : 'dark';
            applyTheme(currentTheme === 'light' ? 'dark' : 'light');
        });

        applyTheme(document.documentElement.dataset.theme === 'light' ? 'light' : 'dark');
        loadProducts();
        loadTransactions();
    </script>
</body>
</html>
