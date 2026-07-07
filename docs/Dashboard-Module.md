# Laporan Perubahan: Modul Dashboard

**Nama Fitur:** Halaman Dashboard `/dashboard`
**Developer:** [Nama]
**Tanggal:** 6 Juli 2026
**Akses:** `http://127.0.0.1:8000/dashboard`

---

## Ringkasan Perubahan

| # | File | Jenis | Keterangan |
|---|------|-------|------------|
| 1 | `app/Http/Controllers/DashboardController.php` | **BARU** | Controller untuk mengolah data dashboard |
| 2 | `resources/views/dashboard/index.blade.php` | **BARU** | Tampilan halaman dashboard |
| 3 | `routes/web.php` | **DIEDIT** | Registrasi route `/dashboard` |
| 4 | `app/Models/Transaction.php` | **DIEDIT** | Tambah `created_at` & `updated_at` ke `$fillable` |
| 5 | `database/seeders/TransactionSeeder.php` | **DIEDIT** | Fix insert timestamp historis |

---

## 1. File Baru: `app/Http/Controllers/DashboardController.php`

**Tujuan:** Controller khusus untuk menyediakan data bagi halaman dashboard.

### Method: `index()`

Method ini mengumpulkan 7 jenis data sekaligus:

#### a. Ringkasan Hari Ini
```php
$todayStart = Carbon::today();                // 00:00:00 hari ini
$todayEnd   = Carbon::today()->endOfDay();    // 23:59:59 hari ini

$todayQuery = Transaction::whereBetween('created_at', [$todayStart, $todayEnd]);

$summary = [
    'revenue' => $todayQuery->sum('total') ?: 0,   // total penjualan hari ini
    'count'   => $todayQuery->count(),               // jumlah transaksi hari ini
    'items'   => TransactionDetail::whereHas(...)    // total item terjual hari ini
                  ->sum('quantity') ?: 0,
];
```

**Mengapa pakai `endOfDay()`?** Awalnya pakai `Carbon::now()`, tapi karena `now()` bisa lebih kecil dari `created_at` transaksi (misal transaksi jam 15:10, dicek jam 15:00), transaksi tidak terhitung. Dengan `endOfDay()` (23:59:59), semua transaksi hari ini pasti tercover.

#### b. Grafik 7 Hari Terakhir
```php
$dates = collect();
for ($i = 6; $i >= 0; $i--) {
    $dates->push(Carbon::today()->subDays($i));  // H-6 sampai hari ini
}

$raw = Transaction::whereBetween('created_at', [$dates->first(), $todayEnd])
    ->selectRaw('DATE(created_at) as tgl, SUM(total) as total')
    ->groupBy('tgl')
    ->orderBy('tgl')
    ->get()
    ->keyBy('tgl');

$chartLabels = $dates->map(fn($d) => $d->format('d M'));
$chartData   = $dates->map(fn($d) => (int) ($raw[$d->format('Y-m-d')]->total ?? 0));
```

Menampilkan data 7 hari dalam format label (contoh: "30 Jun", "01 Jul", ...) dan nilai pendapatan. Jika hari tertentu tidak ada transaksi, tetap muncul dengan nilai 0.

#### c. Produk Terlaris (Top 5)
```php
$topProducts = DB::table('transaction_detail')
    ->join('products', 'transaction_detail.product_id', '=', 'products.id')
    ->selectRaw('products.name, products.sku,
                 SUM(transaction_detail.quantity) as total_qty,
                 SUM(transaction_detail.amount) as total_amount')
    ->groupBy('products.id', 'products.name', 'products.sku')
    ->orderByDesc('total_qty')
    ->limit(5)
    ->get();
```

Query menggabungkan tabel `transaction_detail` dan `products` lalu mengelompokkan berdasarkan produk, menghitung total kuantitas terjual, dan mengambil 5 produk teratas.

#### d. Stok Menipis
```php
$lowStock = Product::where('is_active', true)
    ->whereColumn('stock_quantity', '<=', 'min_stock')
    ->orderBy('stock_quantity')
    ->limit(5)
    ->get();
```

Mengambil produk aktif yang stoknya sudah mencapai atau di bawah batas minimum.

#### e. Transaksi Terbaru
```php
$recent = Transaction::with('details')
    ->latest()
    ->limit(5)
    ->get()
    ->map(fn($t) => [
        'code'  => 'TRX-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
        'time'  => $t->created_at->format('H:i'),
        'date'  => $t->created_at->format('d M'),
        'items' => $t->details->sum('quantity'),
        'total' => $t->total,
    ]);
```

5 transaksi terbaru diformat untuk ditampilkan di panel samping.

---

## 2. File Baru: `resources/views/dashboard/index.blade.php`

**Tujuan:** Halaman dashboard dengan tampilan konsisten mengikuti style aplikasi yang sudah ada (mengacu pada `returns/index.blade.php` dan `transactions/index.blade.php`).

### Teknologi:
- **Tailwind CSS** (CDN) — styling konsisten dengan halaman lain
- **Chart.js** (CDN) — grafik penjualan 7 hari
- **Theme support** — dark/light mode via `localStorage`

### Struktur Halaman:

```
┌──────────────────────────────────────────────────────┐
│  HERO                                               │
│  Dashboard                                         │
│  "Halo, Admin" + Jam Digital Live                  │
│  Tombol "Buka POS →"                               │
├──────────────────────────────────────────────────────┤
│  SUMMARY CARDS (4 kolom)                            │
│  ┌──────────┬──────────┬──────────┬──────────┐      │
│  │Pendapatan│ Transaksi│Item Terj.│  Kasir   │      │
│  │  Rp60rb  │    1     │    8     │  Admin   │      │
│  └──────────┴──────────┴──────────┴──────────┘      │
├────────────────────────────┬─────────────────────────┤
│  CHART (span 3 kolom)      │  STOK MENIPIS           │
│  [Bar Chart - 7 hari]      │  • Produk A — 2         │
│                             │  • Produk B — 0  (merah)│
│                             │  "Semua stok aman"      │
├────────────────────────────┴─────────────────────────┤
│  3 KOLOM GRID                                        │
│  ┌──────────────┬──────────────┬──────────────────┐   │
│  │ PRODUK       │ TRANSAKSI    │ QUICK ACTIONS     │   │
│  │ TERLARIS     │ TERBARU      │                   │   │
│  │              │              │ Buka POS          │   │
│  │ #1 Indomie   │ TRX-0005     │ Transaksi         │   │
│  │ #2 Aqua      │ TRX-0004     │ Laporan           │   │
│  │ #3 Teh Kotak │ TRX-0003     │ Stok              │   │
│  │ ...          │ ...          │ Retur             │   │
│  │              │              │ Profil            │   │
│  └──────────────┴──────────────┴──────────────────┘   │
├──────────────────────────────────────────────────────┤
│  Footer: ERP POS · 2026                              │
└──────────────────────────────────────────────────────┘
```

### Fitur Khusus View:

**Jam Digital Live:**
```javascript
function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').textContent =
        now.toLocaleTimeString('id-ID', { hour12: false });
}
setInterval(updateClock, 1000);
```

**Chart.js Bar Chart:**
```javascript
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(45, 212, 191, 0.25)',
            borderColor: '#2dd4bf',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' } }
        }
    }
});
```

---

## 3. Edit: `routes/web.php`

**Perubahan:** +2 baris

```php
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
```

**Note:** Route `/` tetap redirect ke `/pos` (tidak berubah). Dashboard bisa diakses melalui URL `/dashboard` tanpa mengganggu alur kerja kasir.

---

## 4. Edit: `app/Models/Transaction.php`

**Sebelum:**
```php
protected $fillable = ['total', 'transaction_date'];
```

**Sesudah:**
```php
protected $fillable = ['total', 'transaction_date', 'created_at', 'updated_at'];
```

**Alasan:** Saat seeder mencoba menyimpan transaksi dengan `Transaction::create([... 'created_at' => $timestamp ...])`, nilai `created_at` diabaikan karena tidak ada di `$fillable`. Akibatnya semua transaksi punya timestamp `now()` yang sama, data historis di grafik tidak akurat, dan beberapa query `whereBetween` tidak menghasilkan data yang benar.

---

## 5. Edit: `database/seeders/TransactionSeeder.php`

**Masalah:** `Transaction::create()` hanya mengisi kolom yang ada di `$fillable`. Meskipun sudah ditambah `created_at`/`updated_at` ke `$fillable`, tetap ada risiko jika ada perubahan model di masa depan.

**Solusi:** Gunakan `DB::table('transaction')->insertGetId()` untuk bypass Eloquent dan langsung insert ke database.

**Sebelum:**
```php
$transaction = Transaction::create([
    'total' => 0,
    'created_at' => $timestamp,     // diabaikan (tidak di fillable saat itu)
    'updated_at' => $timestamp,     // diabaikan
]);
```

**Sesudah:**
```php
$transactionId = DB::table('transaction')->insertGetId([
    'total' => 0,
    'created_at' => $timestamp,
    'updated_at' => $timestamp,
]);
$transaction = Transaction::find($transactionId);
```

Dengan perubahan ini, seeder berhasil membuat 5 transaksi dengan tanggal historis yang benar:
| ID | created_at | Keterangan |
|----|------------|------------|
| 1 | Hari ini 15:10 | Transaksi hari ini |
| 2 | Kemarin 10:30 | H-1 |
| 3 | 2 hari lalu 19:15 | H-2 |
| 4 | 5 hari lalu 08:45 | H-5 |
| 5 | 10 hari lalu 13:05 | H-10 |

---

## 6. Perbaikan Bug: Query `whereBetween`

**Masalah:** Query menggunakan `$now = Carbon::now()` sebagai batas atas. Jika ada transaksi dengan `created_at` lebih besar dari `now()` (misal transaksi jam 15:10, dicek jam 15:00), transaksi tidak terhitung.

**Perbaikan:** Ganti `$now` dengan `$todayEnd = Carbon::today()->endOfDay()` (23:59:59).

```php
// SEBELUM
$today = Carbon::today();
$now   = Carbon::now();
Transaction::whereBetween('created_at', [$today, $now]);

// SESUDAH
$todayStart = Carbon::today();                 // 2026-07-06 00:00:00
$todayEnd   = Carbon::today()->endOfDay();     // 2026-07-06 23:59:59
Transaction::whereBetween('created_at', [$todayStart, $todayEnd]);
```

---

## 7. Perintah Penting

Setelah merge, jalankan perintah berikut untuk migrasi + seed ulang:

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
1. Menghapus semua tabel
2. Menjalankan semua migration dari awal
3. Mengisi data default: 2 kasir, 8 produk, 5 transaksi historis

---

## Tidak Ada Konflik dengan Modul Lain

| File | Dampak ke siswa lain |
|------|---------------------|
| `DashboardController.php` | ✅ File baru — tidak mempengaruhi file siapapun |
| `dashboard/index.blade.php` | ✅ File baru — tidak mempengaruhi file siapapun |
| `routes/web.php` | ✅ Hanya menambah route `/dashboard` — tidak mengubah route existing |
| `Transaction.php` | ✅ Menambah `$fillable` — tidak mengubah behavior existing, hanya menambah opsi |
| `TransactionSeeder.php` | ✅ Fix insert — data seeder jadi lebih akurat, tidak mengganggu kode lain |

**Kesimpulan:** Semua perubahan dibuat dengan prinsip **zero conflict**. Tidak ada file siswa lain yang diubah atau dipengaruhi.
