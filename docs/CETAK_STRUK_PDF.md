# Panduan Cetak Struk PDF

## Overview

Fitur ini memungkinkan untuk mencetak struk penjualan dalam format PDF dengan template yang sesuai untuk POS (Point of Sales) system.

## Instalasi

Package yang digunakan: **barryvdh/laravel-dompdf**

Sudah terinstall dan siap digunakan.

## Penggunaan

### 1. Preview Struk (View di Browser)

**Route:** `GET /transactions/{id}/struk-preview`

**Contoh URL:**

```
http://localhost:8000/transactions/1/struk-preview
```

Fungsi ini menampilkan struk langsung di browser dengan format yang sesuai untuk cetak.

### 2. Download Struk PDF

**Route:** `GET /transactions/{id}/print-pdf`

**Contoh URL:**

```
http://localhost:8000/transactions/1/print-pdf
```

Fungsi ini akan men-download file PDF dengan nama `struk_[transaction_id].pdf`

## Struktur File

### Model: `app/Models/Transaction.php`

- Ditambahkan relasi `details()` untuk mengakses `TransactionDetail`

### Controller: `app/Http/Controllers/TransactionController.php`

- Method `printPDF($id)` - Generate dan download PDF
- Method `stukPreview($id)` - Preview struk di browser

### View Template: `resources/views/struk.blade.php`

- Template struk berformat receipt untuk POS
- Responsive untuk ukuran kertas 80mm (standar thermal printer)
- Menampilkan:
    - Nomor Transaksi
    - Tanggal & Waktu
    - Detail Produk (Qty, Harga, Subtotal)
    - Total Barang & Total Harga

### Routes: `routes/web.php`

```php
Route::get('/transactions/{id}/print-pdf', [TransactionController::class, 'printPDF'])->name('transaction.print-pdf');
Route::get('/transactions/{id}/struk-preview', [TransactionController::class, 'stukPreview'])->name('transaction.struk-preview');
```

## Customization

### Mengubah Template Struk

Edit file `resources/views/struk.blade.php`:

- Ubah header/footer sesuai kebutuhan toko
- Ubah styling (font, warna, layout)
- Tambah informasi yang diperlukan (cashier name, payment method, dll)

### Mengubah Format PDF

Edit di `TransactionController.php`:

```php
$pdf = Pdf::loadView('struk', ['transaction' => $transaction])
    ->setPaper('a4', 'portrait'); // Ubah ukuran kertas jika perlu
```

## Contoh Penggunaan di Frontend

### HTML Button

```html
<a
    href="{{ route('transaction.print-pdf', $transaction->id) }}"
    class="btn btn-primary"
>
    <i class="fas fa-print"></i> Print PDF
</a>

<a
    href="{{ route('transaction.struk-preview', $transaction->id) }}"
    class="btn btn-info"
    target="_blank"
>
    <i class="fas fa-eye"></i> Preview
</a>
```

### JavaScript

```javascript
// Print PDF
function printStrukPDF(transactionId) {
    window.location.href = `/transactions/${transactionId}/print-pdf`;
}

// Preview
function previewStruk(transactionId) {
    window.open(`/transactions/${transactionId}/struk-preview`, "_blank");
}
```

## Tips Penggunaan di Thermal Printer

1. Ukuran kertas sudah dikonfigurasi untuk 80mm (standar thermal printer)
2. Font menggunakan Courier New untuk tampilan monospace yang sesuai receipt
3. Jika ingin printing langsung ke printer tanpa dialog, gunakan JavaScript:

```javascript
function printStrukDirect(transactionId) {
    fetch(`/transactions/${transactionId}/struk-preview`)
        .then((response) => response.text())
        .then((html) => {
            const newWindow = window.open("", "", "height=400,width=600");
            newWindow.document.write(html);
            newWindow.print();
        });
}
```

## Troubleshooting

### PDF tidak tergenerate

- Pastikan relasi `details()` di model Transaction sudah benar
- Check apakah transaction dengan ID tersebut ada di database

### Template tidak sesuai

- Check di `resources/views/struk.blade.php`
- Pastikan data yang ditampilkan sesuai dengan field di database

### Font tidak support

- Edit file `resources/views/struk.blade.php` bagian `<style>`
- Ganti font yang lebih universal: Arial, Times New Roman, atau Courier New
