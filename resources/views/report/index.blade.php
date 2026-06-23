<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Penjualan - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>
    <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-6 lg:px-8">
        {{-- Bagian judul halaman --}}
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">Dashboard</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Laporan Penjualan</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Ringkasan transaksi yang tersimpan di database, dihitung langsung dari data kasir.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('pos.index') }}" class="rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">Ke Kasir</a>
                    <button id="refreshButton" class="rounded-2xl border border-cyan-400/40 bg-cyan-400/15 px-4 py-3 text-sm font-medium text-cyan-200 transition hover:bg-cyan-400/25">Muat Ulang</button>
                </div>
            </div>
            <p id="status" class="mt-4 text-sm text-slate-400">Memuat laporan...</p>
        </section>

        {{-- Kartu ringkasan angka utama --}}
        <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="text-sm text-slate-400">Total Pendapatan</div>
                <div id="totalRevenue" class="mt-2 text-2xl font-semibold text-emerald-300">Rp0</div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="text-sm text-slate-400">Jumlah Transaksi</div>
                <div id="totalTransactions" class="mt-2 text-2xl font-semibold text-white">0</div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="text-sm text-slate-400">Rata-rata / Transaksi</div>
                <div id="averageTransaction" class="mt-2 text-2xl font-semibold text-white">Rp0</div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="text-sm text-slate-400">Item Terjual</div>
                <div id="totalItems" class="mt-2 text-2xl font-semibold text-white">0</div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1fr_1.4fr]">
            {{-- Daftar produk paling laris --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <h2 class="mb-4 text-lg font-semibold text-white">Produk Terlaris</h2>
                <div id="bestSellers" class="space-y-3 text-sm text-slate-300"></div>
            </div>

            {{-- Tabel transaksi terbaru --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <h2 class="mb-4 text-lg font-semibold text-white">Transaksi Terbaru</h2>
                <div class="overflow-hidden rounded-2xl border border-white/10">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-900/90 text-slate-400">
                            <tr>
                                <th class="px-3 py-3">ID</th>
                                <th class="px-3 py-3">Tanggal</th>
                                <th class="px-3 py-3">Jumlah Item</th>
                                <th class="px-3 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTable" class="divide-y divide-white/5 bg-slate-950/60"></tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Format angka menjadi mata uang Rupiah.
        const moneyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });
        const formatMoney = (value) => moneyFormatter.format(Number(value || 0));

        const refs = {
            status: document.getElementById('status'),
            refreshButton: document.getElementById('refreshButton'),
            totalRevenue: document.getElementById('totalRevenue'),
            totalTransactions: document.getElementById('totalTransactions'),
            averageTransaction: document.getElementById('averageTransaction'),
            totalItems: document.getElementById('totalItems'),
            bestSellers: document.getElementById('bestSellers'),
            transactionTable: document.getElementById('transactionTable'),
        };

        // Pembungkus fetch JSON sederhana, mengembalikan isi "data" dari respons.
        const fetchJson = async (url) => {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload?.message || 'Gagal mengambil data.');
            }

            return payload.data ?? [];
        };

        // Ubah angka tanggal dari database menjadi format lokal Indonesia.
        const formatDate = (value) => {
            if (!value) {
                return '-';
            }
            const date = new Date(value);
            return Number.isNaN(date.getTime()) ? '-' : date.toLocaleString('id-ID');
        };

        // Hitung ringkasan dan tampilkan ke layar.
        const renderReport = (transactions, productMap) => {
            // Total pendapatan dan jumlah transaksi.
            const totalRevenue = transactions.reduce((sum, trx) => sum + Number(trx.total || 0), 0);
            const totalTransactions = transactions.length;
            const average = totalTransactions > 0 ? totalRevenue / totalTransactions : 0;

            // Akumulasi jumlah item terjual sekaligus kumpulkan penjualan per produk.
            let totalItems = 0;
            const soldPerProduct = {};

            transactions.forEach((trx) => {
                (trx.details ?? []).forEach((detail) => {
                    const quantity = Number(detail.quantity || 0);
                    totalItems += quantity;
                    soldPerProduct[detail.product_id] = (soldPerProduct[detail.product_id] || 0) + quantity;
                });
            });

            refs.totalRevenue.textContent = formatMoney(totalRevenue);
            refs.totalTransactions.textContent = String(totalTransactions);
            refs.averageTransaction.textContent = formatMoney(average);
            refs.totalItems.textContent = String(totalItems);

            // Ambil 5 produk dengan jumlah terjual terbanyak.
            const bestSellers = Object.entries(soldPerProduct)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);

            refs.bestSellers.innerHTML = bestSellers.length === 0
                ? '<p class="text-slate-400">Belum ada penjualan.</p>'
                : bestSellers.map(([productId, quantity]) => {
                    const name = productMap[productId] || `Produk #${productId}`;
                    return `
                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3">
                            <span class="font-medium text-white">${name}</span>
                            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">${quantity} terjual</span>
                        </div>
                    `;
                }).join('');

            // Tampilkan transaksi terbaru di tabel.
            refs.transactionTable.innerHTML = transactions.length === 0
                ? '<tr><td colspan="4" class="px-3 py-6 text-center text-slate-400">Belum ada transaksi.</td></tr>'
                : transactions.map((trx) => `
                    <tr>
                        <td class="px-3 py-3 font-medium text-white">#${trx.id}</td>
                        <td class="px-3 py-3 text-slate-300">${formatDate(trx.created_at)}</td>
                        <td class="px-3 py-3 text-slate-300">${(trx.details ?? []).length} item</td>
                        <td class="px-3 py-3 text-right font-semibold text-emerald-300">${formatMoney(trx.total)}</td>
                    </tr>
                `).join('');
        };

        // Muat data transaksi dan produk lalu hitung laporannya.
        const loadReport = async () => {
            refs.status.textContent = 'Memuat laporan...';

            try {
                // Memakai endpoint yang sudah ada: /transactions dan /products.
                const [transactions, products] = await Promise.all([
                    fetchJson('/transactions'),
                    fetchJson('/products'),
                ]);

                // Buat peta id produk ke nama agar produk terlaris mudah dibaca.
                const productMap = {};
                products.forEach((product) => {
                    productMap[product.id] = product.name;
                });

                renderReport(transactions, productMap);
                refs.status.textContent = `Menampilkan ${transactions.length} transaksi.`;
            } catch (error) {
                refs.status.textContent = error.message || 'Terjadi kesalahan saat memuat laporan.';
            }
        };

        refs.refreshButton.addEventListener('click', loadReport);
        loadReport();
    </script>
</body>
</html>
