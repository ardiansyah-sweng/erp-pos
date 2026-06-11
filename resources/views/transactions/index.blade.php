<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - ERP POS</title>
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

        html[data-theme="light"] .bg-slate-950,
        html[data-theme="light"] .bg-slate-950\/70,
        html[data-theme="light"] .bg-slate-950\/60,
        html[data-theme="light"] .bg-slate-900\/80 {
            background-color: #ffffff !important;
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
        html[data-theme="light"] .text-slate-400 {
            color: #64748b !important;
        }

        html[data-theme="light"] .text-cyan-300\/80,
        html[data-theme="light"] .text-cyan-300\/70 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] .text-emerald-300 {
            color: #047857 !important;
        }

        html[data-theme="light"] input {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] .bg-cyan-400\/10 {
            background-color: #ecfeff !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

    <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-6 lg:px-8">
        @php
            $totalItems = $transactions->sum(fn ($transaction) => $transaction->details->sum('quantity'));
            $totalAmount = $transactions->sum('total');
            $selectedDateLabel = $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Semua';
            $transactionPayload = $transactions->map(fn ($transaction) => [
                'id' => $transaction->id,
                'code' => 'TRX-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT),
                'created_at' => $transaction->created_at?->translatedFormat('d M Y, H.i'),
                'total' => $transaction->total,
                'payment_method' => $transaction->payment_method ?? 'cash',
                'discount_amount' => $transaction->discount_amount ?? 0,
                'cash_tendered' => $transaction->cash_tendered ?? 0,
                'change_amount' => $transaction->change_amount ?? 0,
                'details' => $transaction->details->map(fn ($detail) => [
                    'name' => $detail->product?->name ?? 'Produk #' . $detail->product_id,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'amount' => $detail->amount,
                ])->values(),
            ])->values();
        @endphp

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">ERP POS</p>
                    <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Riwayat transaksi</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">Lihat semua transaksi yang sudah tersimpan dan filter berdasarkan tanggal transaksi.</p>
                    <a href="{{ route('pos.index') }}" class="mt-4 inline-flex items-center justify-center rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                        Kembali ke POS
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Transaksi</div>
                        <div class="mt-1 text-xl font-semibold text-white">{{ $transactions->count() }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Tanggal</div>
                        <div class="mt-1 text-xl font-semibold text-white">{{ $selectedDateLabel }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Item</div>
                        <div class="mt-1 text-xl font-semibold text-white">{{ $totalItems }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3">
                        <div class="text-slate-400">Total</div>
                        <div class="mt-1 text-xl font-semibold text-emerald-300">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
            <form id="dateFilterForm" method="GET" action="{{ route('transactions.index') }}" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-medium text-white">Filter transaksi</p>
                    <p class="mt-1 text-sm text-slate-400">{{ $selectedDate ? 'Menampilkan transaksi pada tanggal terpilih.' : 'Menampilkan semua tanggal transaksi.' }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <label id="dateFilterControl" for="date" class="flex cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 transition focus-within:border-cyan-400 hover:border-cyan-400/50">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300/70">Tanggal</span>
                        <input id="date" name="date" type="date" value="{{ $selectedDate }}" class="w-36 border-0 bg-transparent p-0 text-sm text-white outline-none">
                    </label>
                    <a href="{{ route('transactions.export', array_filter(['date' => $selectedDate])) }}" class="inline-flex items-center justify-center rounded-full border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm font-medium text-emerald-200 transition hover:border-emerald-300 hover:text-white">
                        Export Detail CSV
                    </a>
                    @if ($selectedDate)
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/10 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-200 transition hover:border-cyan-300 hover:text-white">
                            Reset filter
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="grid gap-3">
            @forelse ($transactions as $transaction)
                @php
                    $details = $transaction->details ?? collect();
                    $itemCount = $details->sum('quantity');
                    $transactionCode = 'TRX-' . str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
                @endphp

                <article data-show-transaction="{{ $transaction->id }}" role="button" tabindex="0" class="cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 shadow-xl shadow-black/20 backdrop-blur-xl transition hover:border-cyan-400/50 hover:bg-white/10">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">{{ $transactionCode }}</h2>
                            <p class="mt-1 text-sm text-slate-400">{{ $transaction->created_at?->translatedFormat('d M Y, H.i') }}</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3 md:min-w-72 md:justify-end md:text-right">
                            <div>
                                <p class="text-sm text-slate-400">{{ $itemCount }} item</p>
                                <p class="text-lg font-semibold text-emerald-300">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                            </div>
                            <button type="button" class="rounded-full border border-white/10 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">
                                Detail
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/5 p-8 text-center text-sm text-slate-400 backdrop-blur-xl">
                    Belum ada transaksi yang tersimpan.
                </div>
            @endforelse
        </section>
    </main>

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
        const transactions = @json($transactionPayload);
        const moneyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        const formatMoney = (value) => moneyFormatter.format(Number(value || 0));
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));
        const modal = document.getElementById('transactionModal');
        const modalTitle = document.getElementById('transactionModalTitle');
        const modalDate = document.getElementById('transactionModalDate');
        const modalItems = document.getElementById('transactionModalItems');
        const modalPayment = document.getElementById('transactionModalPayment');
        const modalTotal = document.getElementById('transactionModalTotal');
        const closeModalButton = document.getElementById('closeTransactionModal');

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        const openModal = (transaction) => {
            modalTitle.textContent = transaction.code;
            modalDate.textContent = transaction.created_at || '-';
            modalTotal.textContent = formatMoney(transaction.total);
            modalPayment.innerHTML = `
                <div>Metode: <span class="font-semibold text-white">${escapeHtml(transaction.payment_method || 'cash')}</span></div>
                <div>Cash: <span class="font-semibold text-white">${formatMoney(transaction.cash_tendered)}</span></div>
                <div>Diskon: <span class="font-semibold text-white">${formatMoney(transaction.discount_amount)}</span></div>
                <div>Kembalian: <span class="font-semibold text-white">${formatMoney(transaction.change_amount)}</span></div>
            `;
            modalItems.innerHTML = transaction.details.length ? transaction.details.map((detail) => `
                <div class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
                    <div>
                        <div class="font-medium text-white">${escapeHtml(detail.name)}</div>
                        <div class="text-xs text-slate-400">${detail.quantity} x ${formatMoney(detail.price)}</div>
                    </div>
                    <div class="font-semibold text-emerald-300">${formatMoney(detail.amount)}</div>
                </div>
            `).join('') : `
                <div class="px-4 py-4 text-sm text-slate-400">Detail item tidak tersedia.</div>
            `;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        document.querySelectorAll('[data-show-transaction]').forEach((button) => {
            const showTransaction = () => {
                const transaction = transactions.find((item) => String(item.id) === button.dataset.showTransaction);

                if (transaction) {
                    openModal(transaction);
                }
            };

            button.addEventListener('click', showTransaction);
            button.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    showTransaction();
                }
            });
        });

        document.getElementById('date')?.addEventListener('change', () => {
            document.getElementById('dateFilterForm')?.submit();
        });

        document.getElementById('dateFilterControl')?.addEventListener('click', () => {
            const dateInput = document.getElementById('date');

            if (dateInput?.showPicker) {
                dateInput.showPicker();
                return;
            }

            dateInput?.focus();
        });

        closeModalButton.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
