<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <style>
        body { font-family: sans-serif; padding: 24px; max-width: 600px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px; }
        .error { color: red; font-size: 13px; }
        .btn { padding: 8px 20px; background: #0f766e; color: white; border: none; border-radius: 4px; cursor: pointer; }
        a { color: #0f766e; }
    </style>
</head>
<body>
    <h1>Tambah Produk</h1>
    <a href="{{ route('products.index') }}">← Kembali</a>
    <br><br>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <label>Kode Produk (6 karakter)</label>
        <input type="text" name="product_id" value="{{ old('product_id') }}" maxlength="6">
        @error('product_id') <span class="error">{{ $message }}</span> @enderror

        <label>Barcode</label>
        <input type="text" name="barcode" value="{{ old('barcode') }}">
        @error('barcode') <span class="error">{{ $message }}</span> @enderror

        <label>Nama Produk</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name') <span class="error">{{ $message }}</span> @enderror

        <label>Deskripsi</label>
        <textarea name="description" rows="3">{{ old('description') }}</textarea>

        <label>ID Kategori</label>
        <input type="number" name="category_id" value="{{ old('category_id') }}">
        @error('category_id') <span class="error">{{ $message }}</span> @enderror

        <label>Nama Kategori</label>
        <input type="text" name="category_name" value="{{ old('category_name') }}">
        @error('category_name') <span class="error">{{ $message }}</span> @enderror

        <label>Satuan</label>
        <input type="text" name="unit" value="{{ old('unit') }}">
        @error('unit') <span class="error">{{ $message }}</span> @enderror

        <label>Harga Modal</label>
        <input type="number" name="cost_price" value="{{ old('cost_price') }}">
        @error('cost_price') <span class="error">{{ $message }}</span> @enderror

        <label>Harga Jual</label>
        <input type="number" name="selling_price" value="{{ old('selling_price') }}">
        @error('selling_price') <span class="error">{{ $message }}</span> @enderror

        <label>Stok</label>
        <input type="number" name="stock_quantity" value="{{ old('stock_quantity') }}">
        @error('stock_quantity') <span class="error">{{ $message }}</span> @enderror

        <label>Minimum Stok</label>
        <input type="number" name="min_stock" value="{{ old('min_stock') }}">
        @error('min_stock') <span class="error">{{ $message }}</span> @enderror

        <br><br>
        <button type="submit" class="btn">Simpan</button>
    </form>
</body>
</html>