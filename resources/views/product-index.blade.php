<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Produk</title>
    <style>
        body { font-family: sans-serif; padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f0f0f0; }
        .btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .btn-primary { background: #0f766e; color: white; }
        .btn-warning { background: #ca8a04; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .alert { padding: 12px; background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 6px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Daftar Produk</h1>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    <br><br>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->product_id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category_name }}</td>
                <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td style="display:flex; gap:6px;">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-primary">Detail</a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    {{ $products->links() }}
</body>
</html>