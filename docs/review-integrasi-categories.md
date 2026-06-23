# Integrasi Fitur Kategori Produk — Review & Justifikasi

**Proyek:** ERP-POS (Laravel 11)  
**Branch:** `219_Athar`  
**Fitur Awal:** CRUD Categories (dikerjakan sebelumnya)  
**Fitur Lanjutan:** Integrasi categories ke seluruh codebase  

---

## 1. Latar Belakang

Sebelumnya, fitur **CRUD Categories** sudah diimplementasikan secara lengkap:
- Tabel `categories` dengan hierarki parent-child
- Model `Category` dengan relasi `parent()` dan `children()`
- `CategoryService` (CRUD operations)
- `CategoryController` (routes GET/POST/PUT/DELETE `/categories`)
- View `category/index.blade.php` (form + tabel + modal edit)
- 19 test (Unit + Feature)

**Namun**, fitur ini berdiri **terisolasi** — tidak terhubung ke modul lain. Produk tidak bisa dikategorikan, UI POS tidak menampilkan kategori, halaman `/categories` tidak memiliki link navigasi dari halaman lain.

### Masalah yang Ditemukan (Gap Analysis)

| # | File | Masalah | Dampak |
|---|------|---------|--------|
| 1 | `products` table | Tidak ada kolom `category_id` FK | Produk tidak bisa punya kategori |
| 2 | `Model Product` | Tidak ada `category_id` di fillable & relasi `belongsTo` | Tidak bisa CRUD kategori via model |
| 3 | `Model Category` | Tidak ada relasi `hasMany(Product)` | Tidak bisa ambil produk per kategori |
| 4 | `ProductController` | `getProducts()` tidak filter `category_id` | API tidak support filter kategori |
| 5 | `PosController` | Tidak melempar data categories ke view | View tidak bisa render dropdown |
| 6 | `pos/index.blade.php` | Tidak ada dropdown filter, badge kategori, link navigasi | User tidak bisa filter/pindah halaman |
| 7 | Seeder | Tidak ada `CategorySeeder`, produk tanpa `category_id` | Data awal tidak realistis |

---

## 2. Perubahan yang Dilakukan (8 File)

```
[BARU]  database/migrations/2026_06_23_100000_add_category_id_to_products_table.php
[BARU]  database/seeders/CategorySeeder.php
[EDIT]  app/Models/Product.php
[EDIT]  app/Models/Category.php
[EDIT]  app/Http/Controllers/ProductController.php
[EDIT]  app/Http/Controllers/PosController.php
[EDIT]  resources/views/pos/index.blade.php
[EDIT]  database/seeders/ProductSeeder.php
[EDIT]  database/seeders/DatabaseSeeder.php
```

---

### 2.1 Migration: `add_category_id_to_products_table`

**File:** `database/migrations/2026_06_23_100000_add_category_id_to_products_table.php`

```php
$table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
```

| Aspek | Penjelasan |
|-------|-----------|
| **Mengapa nullable?** | Produk tidak wajib punya kategori. Produk lama/seeder sebelumnya tidak punya `category_id` |
| **Mengapa `nullOnDelete`?** | Kalau kategori dihapus, jangan hapus produk — set `category_id` ke null saja |
| **Mengapa migration terpisah?** | Tabel `products` (April) sudah ada sebelum `categories` (Juni). Tidak bisa di-`change` langsung |

---

### 2.2 Model Product: Relasi + Fillable

**File:** `app/Models/Product.php` (+2 tambahan)

```php
// $fillable ditambah:
'category_id',

// Method baru:
public function category()
{
    return $this->belongsTo(Category::class);
}
```

| Perubahan | Mengapa Diperlukan |
|-----------|-------------------|
| `category_id` di `$fillable` | Agar mass-assignment (`create()`, `update()`) bisa set kolom ini |
| `category()` relationship | Agar bisa eager-load kategori: `Product::with('category')->get()` → response JSON otomatis include kategori |

**Kaitan dengan codebase:** `TransactionController::checkout()` dan `ProductController::getProducts()` menggunakan Eloquent query pada model Product. Dengan relasi ini, semua query yang eager-load `category` akan otomatis menyertakan data kategori.

---

### 2.3 Model Category: Relasi ke Product

**File:** `app/Models/Category.php` (+1 tambahan)

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

| Aspek | Penjelasan |
|-------|-----------|
| **Inverse relationship** | Melengkapi `Product → belongsTo(Category)` dengan `Category → hasMany(Product)` |
| **Memungkinkan query** | `Category::with('products')->get()` untuk ambil semua produk dalam kategori tertentu |

**Kaitan dengan codebase:** `CategoryService::getAll()` mengembalikan kategori dengan children. Sekarang bisa juga eager-load produk jika diperlukan untuk laporan stok per kategori.

---

### 2.4 ProductController: Filter + Eager-Load

**File:** `app/Http/Controllers/ProductController.php` (3 perubahan)

```php
// Sebelum:
$search = trim((string) $request->query('search', ''));
$products = Product::query()->where('is_active', true)...

// Sesudah:
$search = trim((string) $request->query('search', ''));
$categoryId = $request->query('category_id');
$products = Product::query()
    ->with('category')                    // ⬅ eager-load
    ->where('is_active', true)
    ->when($categoryId, function ($query) use ($categoryId) {
        $query->where('category_id', $categoryId);  // ⬅ filter
    })
```

| Perubahan | Mengapa |
|-----------|---------|
| `with('category')` | Response JSON sekarang include `category: { id, name }` di tiap produk → UI bisa tampilkan nama kategori |
| `when($categoryId)` | Endpoint `/products?category_id=2` sekarang memfilter produk berdasarkan kategori |
| Keduanya kompatibel | Filter `category_id` dan `search` bisa dikombinasikan |

**Kaitan dengan codebase:** Endpoint ini dipanggil oleh POS frontend (`pos/index.blade.php`) setiap kali user search atau filter. Tanpa perubahan ini, dropdown filter kategori di POS tidak akan berfungsi.

---

### 2.5 PosController: Data Kategori ke View

**File:** `app/Http/Controllers/PosController.php` (import + query)

```php
// Sebelum:
public function index()
{
    return view('pos.index');
}

// Sesudah:
use App\Models\Category;

public function index()
{
    $categories = Category::where('is_active', true)->orderBy('name')->get();
    return view('pos.index', compact('categories'));
}
```

| Aspek | Penjelasan |
|-------|-----------|
| **Filter `is_active`** | Hanya kategori aktif yang muncul di dropdown |
| **Sort `by name`** | Dropdown terurut alfabetis, mudah dicari |
| **Compact ke view** | Variable `$categories` tersedia di Blade untuk render `<select>` |

**Kaitan dengan codebase:** POS view (`pos/index.blade.php`) adalah halaman utama aplikasi — semua transaksi dimulai dari sini. Dengan data kategori tersedia, view bisa render dropdown filter.

---

### 2.6 POS View: Dropdown Filter, Badge, Navigasi

**File:** `resources/views/pos/index.blade.php` (4 perubahan)

#### 2.6.1 Dropdown Filter Kategori (baris 178-186)

```blade
<div class="mt-3">
    <label class="text-sm text-slate-300" for="categoryFilter">Kategori</label>
    <select id="categoryFilter" ...>
        <option value="">Semua Kategori</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
    </select>
</div>
```

| Mengapa di sini? | Di atas daftar produk, di bawah search bar — user filter dulu baru lihat hasil |
| JS handler | `refs.categoryFilter.addEventListener('change', ...)` → panggil `loadProducts()` dengan param `category_id` |

#### 2.6.2 Badge Kategori di Product Card

```javascript
${product.category
    ? `<span class="...rounded-full border border-purple-400/20 bg-purple-400/10
       px-3 py-1 text-xs font-medium text-purple-200">${product.category.name}</span>`
    : ''}
```

| Mengapa warna ungu? | Membedakan visual dari SKU (cyan) dan stok (green/red/amber) |
| Conditional render | Jika produk tidak punya kategori, badge tidak muncul (backward-compatible) |

#### 2.6.3 Link Navigasi "Kategori" (baris 331-333)

```blade
<a href="{{ route('categories.index') }}" 
   class="...border-purple-400/40 bg-purple-400/10...text-purple-200...">
    Kategori
</a>
```

| Mengapa di area Riwayat Transaksi? | Tempat paling strategis — sudah ada "Lihat semua" untuk transaksi, sekarang tambah "Kategori" untuk CRUD |
| Konsistensi | Styling sama dengan "Lihat semua" tapi warna ungu biar beda |

#### 2.6.4 JavaScript Filter (3 titik)

```javascript
// loadProducts() signature diubah:
const loadProducts = async (search = '', categoryId = '') => {
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (categoryId) params.set('category_id', categoryId);
    ...
}

// Event listener baru:
refs.categoryFilter.addEventListener('change', () =>
    loadProducts(refs.productSearch.value.trim(), refs.categoryFilter.value));
```

| Perubahan | Mengapa |
|-----------|---------|
| `URLSearchParams` | Konstruksi query string lebih bersih daripada manual concatenation |
| Event listener `change` | Auto-load ulang produk setiap ganti kategori |
| Semua pemanggil diperbarui | `productSearch`, `refreshProducts`, `checkout` success, initial load — semua pass `categoryId` |

---

### 2.7 CategorySeeder: Data Awal Kategori

**File:** `database/seeders/CategorySeeder.php` (baru)

```
4 Parent Categories:
  ├── Makanan → Makanan Ringan, Makanan Berat
  ├── Minuman → Minuman Dingin, Minuman Panas
  ├── Perlengkapan
  └── Lainnya
```

| Aspek | Penjelasan |
|-------|-----------|
| `updateOrCreate` | Aman dijalankan berkali-kali (idempotent) |
| Struktur 2-level | Makanan dan Minuman punya child, Perlengkapan & Lainnya tidak — menunjukkan hierarki nyata |
| Lookup by name | Child category cari parent by `name` dulu, bukan hardcode ID |

**Kaitan dengan codebase:** CategorySeeder dipanggil **pertama** di `DatabaseSeeder`, sebelum ProductSeeder (karena produk perlu `category_id` yang valid).

---

### 2.8 ProductSeeder: Assign Kategori

**File:** `database/seeders/ProductSeeder.php` (refactor)

```php
// Sebelum: array dengan hardcoded category_id (bisa gagal FK constraint)
// Sesudah: lookup by name
$categoryIds = Category::pluck('id', 'name');

foreach ($products as $product) {
    $categoryName = $product['category'];
    unset($product['category']);
    Product::updateOrCreate(
        ['sku' => $product['sku']],
        $product + [
            'is_active' => true,
            'category_id' => $categoryIds[$categoryName] ?? null,
        ]
    );
}
```

| Mengapa lookup by name? | ID auto-increment tidak bisa diprediksi. `pluck('id', 'name')` ambil ID aktual saat runtime |
| `?? null` fallback | Kalau nama kategori tidak ditemukan, set `null` — tidak gagal |

| Produk | Kategori |
|--------|----------|
| Aqua 600ml | Minuman |
| Indomie Goreng | Makanan |
| Teh Kotak 300ml | Minuman |
| Snack Kentang | Makanan Ringan |
| Susu UHT 250ml | Minuman |
| Biskuit Cokelat | Makanan Ringan |
| Kopi Sachet | Minuman Panas |
| Sabun Cuci Piring | Perlengkapan |

---

### 2.9 DatabaseSeeder: Registrasi Urutan

**File:** `database/seeders/DatabaseSeeder.php`

```php
// Sebelum: semua dikomentari
// Sesudah:
$this->call([
    CategorySeeder::class,     // harus pertama (FK dependency)
    ProductSeeder::class,      // kedua (butuh category_id)
    TransactionSeeder::class,  // ketiga (butuh product_id)
]);
```

| Urutan | Alasan |
|--------|--------|
| 1. CategorySeeder | Produk butuh `category_id` FK ke categories |
| 2. ProductSeeder | Transaksi butuh `product_id` FK ke products |
| 3. TransactionSeeder | Transaksi dependen pada produk & sekarang juga payment_details |

---

## 3. Data Flow — Bagaimana Semuanya Terhubung

```
Database
  ├── categories (8 records)  ←── CategorySeeder
  ├── products (8 records)    ←── ProductSeeder (category_id FK)
  └── transactions            ←── dari POS UI

                     ┌──────────────────┐
                     │   PosController   │
                     │  query categories │
                     └──────┬───────────┘
                            │ compact('categories')
                            ▼
               ┌───────────────────────────┐
               │    pos/index.blade.php     │
               │  ┌─────────────────────┐  │
               │  │ <select> Kategori   │  │  ←── dropdown dari $categories
               │  │ <option> Makanan    │  │
               │  │ <option> Minuman    │  │
               │  └─────────────────────┘  │
               │           │               │
               │           │ onchange       │
               │           ▼               │
               │  loadProducts(search,     │
               │    categoryFilter.value)  │
               │           │               │
               │           ▼               │
               │  GET /products             │
               │    ?category_id=2          │
               └───────────┬───────────────┘
                           │
                           ▼
               ┌───────────────────────────┐
               │   ProductController        │
               │  ->with('category')        │
               │  ->where('category_id', 2) │
               └──────┬────────────────────┘
                      │ JSON: { data: [{ id, name, category: { id, name } }] }
                      ▼
               ┌───────────────────────────┐
               │   Render product cards     │
               │  ┌─────────────────────┐  │
               │  │ Aqua 600ml          │  │
               │  │ [Minuman] ← badge   │  │
               │  └─────────────────────┘  │
               └───────────────────────────┘

Navigasi: [Kategori] → GET /categories → CategoryController@index
```

---

## 4. Verifikasi

```
php artisan migrate:fresh --seed     ✅ 10 tabel, 3 seeder, 0 error
php artisan test                     ✅ 39 tests passed, 82 assertions
```

Semua test yang sudah ada tetap **pass** — tidak ada breaking change karena:
- Kolom `category_id` bersifat `nullable`
- Filter `?category_id` hanya aktif jika parameter diberikan
- Relasi `with('category')` tidak mengubah struktur response, hanya menambah field nested

---

## 5. Kesimpulan

| Dampak | Sebelum | Sesudah |
|--------|---------|---------|
| Produk vs Kategori | Tidak terhubung | FK `category_id`, relasi Eloquent dua arah |
| POS UI — filter | Hanya search text | Search + dropdown kategori |
| POS UI — info | Tidak ada info kategori | Badge ungu di tiap kartu produk |
| POS UI — navigasi | Tidak ada link ke /categories | Tombol "Kategori" di sidebar |
| API `/products` | Hanya filter `?search` | `?search` + `?category_id`, response include `category` |
| Seeder | Kategori tidak ada, produk tanpa kategori | 8 kategori (hierarki), 8 produk terassign |
| Test | 39 pass | 39 pass (tidak ada regresi) |
