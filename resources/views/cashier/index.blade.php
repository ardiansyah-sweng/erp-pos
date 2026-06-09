<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Cashier</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #0f1923;
            color: #e2e8f0;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            padding: 2rem;
        }

        .header {
            margin-bottom: 2rem;
        }

        .header .eyebrow {
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #4fd1a5;
            margin-bottom: 0.5rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        .header p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-top: 0.4rem;
        }

        .stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #1a2535;
            border: 1px solid #2d3f55;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            min-width: 120px;
        }

        .stat-card .label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.3rem;
        }

        .stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        .card {
            background: #1a2535;
            border: 1px solid #2d3f55;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #2d3f55;
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .badge {
            background: #0f2d24;
            color: #4fd1a5;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            border: 1px solid #1d5c43;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 0.85rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            background: #141f2e;
        }

        tbody tr {
            border-top: 1px solid #1e2f42;
            transition: background 0.15s;
        }

        tbody tr:hover {
            background: #1f3045;
        }

        tbody td {
            padding: 1rem 1.5rem;
            font-size: 0.9rem;
            color: #cbd5e1;
        }

        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #0f2d24;
            color: #4fd1a5;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid #1d5c43;
            margin-right: 0.6rem;
            vertical-align: middle;
        }

        .name-cell {
            display: flex;
            align-items: center;
        }

        .no-data {
            text-align: center;
            color: #475569;
            padding: 3rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="header">
        <p class="eyebrow">Manajemen Pengguna</p>
        <h1>Daftar Cashier</h1>
        <p>Kelola seluruh akun cashier yang terdaftar di sistem.</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p class="label">Total Cashier</p>
            <p class="value">{{ count($cashiers) }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Semua Cashier</h2>
            <span class="badge">{{ count($cashiers) }} akun</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashiers as $index => $cashier)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="name-cell">
                            <span class="avatar">{{ strtoupper(substr($cashier->name, 0, 2)) }}</span>
                            {{ $cashier->name }}
                        </div>
                    </td>
                    <td>{{ $cashier->username }}</td>
                    <td>{{ \Carbon\Carbon::parse($cashier->created_at)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">Belum ada data cashier.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>