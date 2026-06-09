<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>
    <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-6 lg:px-8">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Point of Sales</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Kasir cepat, pencarian produk, dan checkout AJAX.</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Cari produk, masukkan ke keranjang, hitung total otomatis, lalu simpan transaksi tanpa reload halaman.</p>
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
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Daftar Produk</h2>
                        <span id="productsMeta" class="text-sm text-slate-400"></span>
                    </div>
                    <div id="productGrid" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">Keranjang</h2>
                        <button id="clearCart" class="text-sm text-rose-300 transition hover:text-rose-200">Kosongkan</button>
                    </div>
                    <div id="cartEmptyState" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/50 px-4 py-8 text-center text-sm text-slate-400">Keranjang masih kosong.</div>
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

                    <button id="checkoutButton" class="mt-5 w-full rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50">Checkout</button>
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
        };

        const refs = {
            productSearch: document.getElementById('productSearch'),
            barcodeSearch: document.getElementById('barcodeSearch'),
            refreshProducts: document.getElementById('refreshProducts'),
            productsStatus: document.getElementById('productsStatus'),
            productsMeta: document.getElementById('productsMeta'),
            productGrid: document.getElementById('productGrid'),
            productCount: document.getElementById('productCount'),
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
            const discount = getDiscount();
            const grandTotal = calculateGrandTotal();
            const change = calculateChange();

            refs.productCount.textContent = String(state.products.length);
            refs.cartCount.textContent = String(state.cart.length);
            refs.subtotalLabel.textContent = formatMoney(subtotal);
            refs.totalLabel.textContent = formatMoney(grandTotal);
            refs.subtotalValue.textContent = formatMoney(subtotal);
            refs.discountValue.textContent = formatMoney(discount);
            refs.grandTotalValue.textContent = formatMoney(grandTotal);
            refs.changeValue.textContent = formatMoney(change);
            refs.checkoutButton.disabled = state.cart.length === 0;
            refs.checkoutStatus.textContent = state.cart.length === 0 ? 'Tambahkan produk ke keranjang terlebih dahulu.' : '';
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
                        Produk tidak ditemukan.
                    </div>
                `;
                refs.productsMeta.textContent = '0 item';
                return;
            }

            refs.productsMeta.textContent = `${state.products.length} item`;

            refs.productGrid.innerHTML = state.products.map((product) => `
                <button type="button" data-product-id="${product.id}" class="group rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-left transition hover:-translate-y-1 hover:border-cyan-400/50 hover:bg-slate-900/90">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">${product.sku || '-'}</div>
                            <h3 class="mt-2 text-base font-semibold text-white">${product.name}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-400">${product.description || 'Tanpa deskripsi'}</p>
                        </div>
                        <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">Stok ${product.stock_quantity}</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="font-semibold text-emerald-300">${formatMoney(product.selling_price)}</span>
                        <span class="text-slate-500 group-hover:text-slate-300">Klik untuk tambah</span>
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
                const queryString = search ? `?search=${encodeURIComponent(search)}` : '';
                const response = await fetchJson(`/api/products${queryString}`);
                state.products = response.data ?? [];
                renderProducts();
                refs.productsStatus.textContent = 'Pilih produk untuk dimasukkan ke keranjang.';
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
                const response = await fetchJson(`/api/products?search=${encodeURIComponent(keyword)}`);
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
        refs.clearCart.addEventListener('click', () => {
            state.cart = [];
            renderCart();
        });
        refs.discountAmount.addEventListener('input', updateSummary);
        refs.cashTendered.addEventListener('input', updateSummary);
        refs.paymentMethod.addEventListener('change', updateSummary);
        refs.checkoutButton.addEventListener('click', checkout);

        loadProducts();
        loadTransactions();
    </script>
</body>
</html>