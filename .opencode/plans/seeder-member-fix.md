# Plan: Fix Data Kosong di Laporan Member

## Langkah 1: Tambah `customer_id` ke $fillable Transaction

**File:** `app/Models/Transaction.php` — baris 11

Ubah dari:
```php
protected $fillable = ['total', 'transaction_date', 'created_at', 'updated_at'];
```
Menjadi:
```php
protected $fillable = ['customer_id', 'total', 'transaction_date', 'created_at', 'updated_at'];
```

> **Kenapa?** Saat `checkout()` memanggil `Transaction::create(['customer_id' => ..., 'total' => ...])`, field `customer_id` diabaikan karena tidak ada di `$fillable`. Akibatnya semua transaksi menyimpan `customer_id = NULL`.

---

## Langkah 2: Buat CustomerSeeder

**File BARU:** `database/seeders/CustomerSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $customers = [
            ['name' => 'Budi Santoso',    'phone' => '081234567890', 'points' => 3200],
            ['name' => 'Siti Rahmawati',  'phone' => '081234567891', 'points' => 1800],
            ['name' => 'Ahmad Hidayat',   'phone' => '081234567892', 'points' => 750],
            ['name' => 'Dewi Lestari',    'phone' => '081234567893', 'points' => 150],
            ['name' => 'Rudi Hermawan',   'phone' => '081234567894', 'points' => 2800],
            ['name' => 'Ani Wijaya',      'phone' => '081234567895', 'points' => 5000],
            ['name' => 'Hendra Gunawan',  'phone' => '081234567896', 'points' => 1200],
            ['name' => 'Rina Marlina',    'phone' => '081234567897', 'points' => 0],
            ['name' => 'Doni Prasetyo',   'phone' => '081234567898', 'points' => 4100],
            ['name' => 'Mega Sari',       'phone' => '081234567899', 'points' => 600],
            ['name' => 'Agus Wijaya',     'phone' => '081234567800', 'points' => 2200],
            ['name' => 'Fitri Handayani', 'phone' => '081234567801', 'points' => 350],
            ['name' => 'Bayu Saputra',    'phone' => '081234567802', 'points' => 1600],
            ['name' => 'Indah Permata',   'phone' => '081234567803', 'points' => 900],
            ['name' => 'Eko Prasetyo',    'phone' => '081234567804', 'points' => 10],
        ];

        $now = now();

        foreach ($customers as $i => $data) {
            $points = $data['points'];

            if ($points >= 3000) {
                $level = 'Platinum';
            } elseif ($points >= 1500) {
                $level = 'Gold';
            } elseif ($points >= 500) {
                $level = 'Silver';
            } else {
                $level = 'Regular';
            }

            Customer::create([
                'customer_code' => 'CUS' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name'          => $data['name'],
                'phone'         => $data['phone'],
                'email'         => $faker->optional(0.6)->email(),
                'address'       => $faker->optional(0.7)->address(),
                'points'        => $points,
                'member_level'  => $level,
                'created_at'    => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at'    => $now,
            ]);
        }

        $this->command->info('Seeder 15 data customer berhasil dibuat.');
    }
}
```

> **Detail:** 15 customer dengan variasi poin 0–5000 → level Regular s/d Platinum. `created_at` tersebar 6 bulan terakhir untuk mengisi chart pertumbuhan. Ada customer dengan 0 transaksi untuk validasi tampilan "Belum ada transaksi".

---

## Langkah 3: Update DatabaseSeeder

**File:** `database/seeders/DatabaseSeeder.php`

Ubah dari:
```php
$this->call([
    CategorySeeder::class,
    CashierSeeder::class,
    ProductSeeder::class,
    TransactionSeeder::class,
]);
```
Menjadi:
```php
$this->call([
    CategorySeeder::class,
    CashierSeeder::class,
    ProductSeeder::class,
    CustomerSeeder::class,    // BARU — sebelum TransactionSeeder
    TransactionSeeder::class,
]);
```

> **PENTING:** CustomerSeeder HARUS sebelum TransactionSeeder agar transaksi bisa merujuk ke customer yang sudah ada.

---

## Langkah 4: Update TransactionSeeder (OPSIONAL — agar transaksi terlink ke customer)

Jika ingin transaksi yang di-seed juga terhubung ke customer, tambahkan 1 baris ini di `TransactionSeeder.php`:

Setelah `$transaction = Transaction::find($transactionId);` (baris ~48), tambah:
```php
$customer = Customer::inRandomOrder()->first();
$transaction->customer_id = $customer->id;
$transaction->save();
```

Dan tambahkan `use App\Models\Customer;` di bagian atas file.

---

## Langkah 5: Jalankan

```bash
php artisan db:seed
```

Atau jika ingin reset database total:
```bash
php artisan migrate:fresh --seed
```

---

## Setelah Seeder: Yang Terlihat di `/reports/members`

| Komponen | Hasil |
|----------|-------|
| Total Member | 15 |
| Member Baru | Tergantung bulan ini |
| Total Poin | ~21.000 |
| Rata-rata Belanja | Terisi (jika transaksi terlink) |
| Chart Level | Regular: 5, Silver: 3, Gold: 4, Platinum: 3 |
| Chart Pertumbuhan | Tersebar 6 bulan |
| Top Lists | Terisi sesuai data |
| Tabel Member | 15 baris dengan pagination |
