<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manajemen Diskon</title>
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

    html[data-theme="light"] .bg-white\/5,
    html[data-theme="light"] .bg-slate-950\/60,
    html[data-theme="light"] .bg-slate-950\/70,
    html[data-theme="light"] .bg-slate-900\/80 {
      background-color: #ffffff !important;
    }

    html[data-theme="light"] .border-white\/10 {
      border-color: #dbe3ea !important;
    }

    html[data-theme="light"] .text-white {
      color: #0f172a !important;
    }

    html[data-theme="light"] .text-slate-200,
    html[data-theme="light"] .text-slate-300,
    html[data-theme="light"] .text-slate-400 {
      color: #64748b !important;
    }

    html[data-theme="light"] input,
    html[data-theme="light"] select,
    html[data-theme="light"] textarea {
      background-color: #ffffff !important;
      color: #0f172a !important;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
  <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-amber-500/20 via-cyan-500/20 to-transparent blur-3xl"></div>
  <main class="relative mx-auto max-w-7xl px-4 py-6 lg:px-8">

    {{-- Header --}}
    <section class="mb-6 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-sm uppercase tracking-[0.35em] text-amber-300/80">Manajemen Diskon</p>
          <h1 class="mt-2 text-3xl font-semibold text-white md:text-4xl">Promo & Diskon Produk.</h1>
          <p class="mt-2 max-w-2xl text-sm text-slate-300">Tambahkan diskon persentase atau nominal untuk produk tertentu dengan rentang tanggal yang dapat diatur.</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('pos.index') }}" class="rounded-full border border-white/10 bg-slate-950/70 px-4 py-2 text-sm font-medium text-slate-300 transition hover:border-cyan-400/50 hover:text-white">Kembali POS</a>
          <a href="{{ route('products.index') }}" class="rounded-full border border-amber-400/40 bg-amber-400/10 px-4 py-2 text-sm font-medium text-amber-200 transition hover:bg-amber-400/20">Daftar Produk</a>
        </div>
      </div>
    </section>

    {{-- Alert --}}
    @if (session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-medium text-emerald-100">
      {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
      {{ $errors->first() }}
    </div>
    @endif

    <section class="grid gap-6 lg:grid-cols-[1.5fr_0.8fr]">

      {{-- Daftar Diskon --}}
      <div class="space-y-5">

        {{-- Search --}}
        <form method="GET" action="{{ route('discounts.index') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
          <label for="search" class="text-sm text-slate-300">Cari diskon atau produk</label>
          <div class="mt-2 flex flex-col gap-3 sm:flex-row">
            <input id="search" name="search" value="{{ $search }}" placeholder="Nama diskon, nama produk, atau SKU" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-amber-400">
            <button class="rounded-2xl border border-amber-400/40 bg-amber-400/15 px-5 py-3 text-sm font-semibold text-amber-100 transition hover:bg-amber-400/25">Cari</button>
          </div>
        </form>

        {{-- Tabel Diskon --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl overflow-hidden">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-900/80 text-slate-400">
              <tr>
                <th class="px-4 py-3">Nama Diskon</th>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3 text-right">Nilai</th>
                <th class="px-4 py-3">Periode</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10 bg-slate-950/60">
              @forelse ($discounts as $discount)
              <tr>
                <td class="px-4 py-3 font-medium text-white">{{ $discount->name }}</td>
                <td class="px-4 py-3">
                  <div class="text-white">{{ $discount->product?->name ?? '-' }}</div>
                  <div class="text-xs text-slate-400">{{ $discount->product?->sku }}</div>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-amber-300">
                  @if ($discount->type === 'percentage')
                  {{ $discount->value }}%
                  @else
                  Rp{{ number_format($discount->value, 0, ',', '.') }}
                  @endif
                </td>
                <td class="px-4 py-3 text-xs text-slate-300">
                  {{ $discount->start_date->format('d M Y') }} –
                  {{ $discount->end_date->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-center">
                  @if ($discount->isCurrentlyActive())
                  <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                  @else
                  <span class="rounded-full bg-slate-700/50 px-3 py-1 text-xs font-semibold text-slate-400">Tidak Aktif</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-center">
                  <form method="POST" action="{{ route('discounts.destroy', $discount) }}" onsubmit="return confirm('Hapus diskon ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-xl border border-rose-400/30 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-300 transition hover:bg-rose-400/20">Hapus</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-400">
                  Belum ada diskon. Tambahkan diskon pertama di panel kanan.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
          <div class="p-4">{{ $discounts->links() }}</div>
        </div>
      </div>

      {{-- Form Tambah Diskon --}}
      <aside class="space-y-5">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
          <h2 class="text-lg font-semibold text-white">Tambah Diskon Baru</h2>
          <form method="POST" action="{{ route('discounts.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
              <label class="text-sm text-slate-300">Produk</label>
              <select name="product_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-amber-400">
                <option value="">-- Pilih Produk --</option>
                @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                  {{ $product->name }} ({{ $product->sku }})
                </option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-300">Nama Diskon</label>
              <input name="name" value="{{ old('name') }}" placeholder="Contoh: Promo Lebaran" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-amber-400">
            </div>
            <div>
              <label class="text-sm text-slate-300">Tipe Diskon</label>
              <select name="type" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-amber-400">
                <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
              </select>
            </div>
            <div>
              <label class="text-sm text-slate-300">Nilai Diskon</label>
              <input name="value" type="number" min="1" value="{{ old('value') }}" placeholder="Contoh: 10 (untuk 10%)" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-amber-400">
            </div>
            <div>
              <label class="text-sm text-slate-300">Tanggal Mulai</label>
              <input name="start_date" type="date" value="{{ old('start_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-amber-400">
            </div>
            <div>
              <label class="text-sm text-slate-300">Tanggal Selesai</label>
              <input name="end_date" type="date" value="{{ old('end_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none focus:border-amber-400">
            </div>
            <div class="flex items-center gap-3">
              <input name="is_active" type="checkbox" value="1" checked id="is_active" class="rounded">
              <label for="is_active" class="text-sm text-slate-300">Aktifkan diskon</label>
            </div>
            <button class="w-full rounded-2xl bg-gradient-to-r from-amber-400 to-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110">
              Simpan Diskon
            </button>
          </form>
        </div>
      </aside>

    </section>
  </main>
</body>

</html>