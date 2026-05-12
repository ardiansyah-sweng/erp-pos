<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk</title>
    <style>
        body { font-family: sans-serif; padding: 24px; max-width: 600px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; width: 40%; color: #555; }
        a { color: #0f766e; }
    </style>
</head>
<body>
    <h1>Detail Produk</h1>
    <a href="{{ route('products.index') }}">← Kembali</a>
    <br><br>

    <table>
        <tr><td>ID</td><td>{{ $product->id }}</td></tr>
        <tr><td>Kode Produk</td><td>{{ $product->product_id }}</td></tr>
        <tr><td>Barcode</td><td>{{ $product->barcode }}</td></tr>
        <tr><td>Nama</td><td>{{ $product->name }}</td></tr>
        <tr><td>Deskripsi</td><td>{{ $product->description ?? '-' }}</td></tr>
        <tr><td>Kategori</td><td>{{ $product->category_name }}</td></tr>
        <tr><td>Satuan</td><td>{{ $product->unit }}</td></tr>
        <tr><td>Harga Modal</td><td>Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td></tr>
        <tr><td>Harga Jual</td><td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td></tr>
        <tr><td>Stok</td><td>{{ $product->stock_quantity }}</td></tr>
        <tr><td>Min. Stok</td><td>{{ $product->min_stock }}</td></tr>
        <tr><td>Status</td><td>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</td></tr>
    </table>

    <br>
    <a href="{{ route('products.edit', $product) }}">✏️ Edit Produk</a>
</body>
</html>