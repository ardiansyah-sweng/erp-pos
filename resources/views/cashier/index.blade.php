<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kasir</title>
    <style>
        table { width: 50%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Data Pencarian Kasir</h2>
    
    <form action="/" method="GET">
        <input type="text" name="q" placeholder="Cari nama..." value="{{ request('q') }}">
        <button type="submit">Cari</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
        </tr>
        @foreach($cashiers as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->name }}</td>
            <td>{{ $c->email }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>