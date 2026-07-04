<h1>Daftar Cashier</h1>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($cashiers as $cashier)
            <tr>
                <td>{{ $cashier->name }}</td>
                <td>{{ $cashier->username }}</td>
                <td>
                    <form action="{{ route('cashier.destroy', $cashier->id) }}" method="post" onsubmit="return confirm('Hapus cashier ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Belum ada cashier.</td>
            </tr>
        @endforelse
    </tbody>
</table>
