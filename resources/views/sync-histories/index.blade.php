<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sinkronisasi - ERP POS</title>

    <style>
        body{
            font-family:'Segoe UI',sans-serif;
            background:#0d1117;
            color:#e6edf3;
            margin:0;
        }

        .header{
            background:#161b22;
            border-bottom:1px solid #21262d;
            padding:24px 32px;
        }

        .header h1{
            margin:0;
            font-size:28px;
        }

        .header p{
            color:#8b949e;
            margin-top:8px;
        }

        .container{
            max-width:1200px;
            margin:auto;
            padding:30px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:15px;
            margin-bottom:25px;
        }

        .card{
            background:#161b22;
            border:1px solid #21262d;
            border-radius:12px;
            padding:20px;
        }

        .card h4{
            margin:0;
            color:#8b949e;
            font-size:13px;
        }

        .card h2{
            margin-top:10px;
        }

        .search-box{
            margin-bottom:20px;
        }

        .search-box input{
            width:300px;
            padding:10px;
            background:#21262d;
            border:1px solid #30363d;
            color:white;
            border-radius:8px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:#161b22;
        }

        th,td{
            padding:14px;
            border-bottom:1px solid #21262d;
        }

        th{
            background:#0d1117;
            text-align:left;
        }

        .badge-success{
            background:#22c55e22;
            color:#22c55e;
            padding:4px 10px;
            border-radius:20px;
        }

        .badge-failed{
            background:#ef444422;
            color:#ef4444;
            padding:4px 10px;
            border-radius:20px;
        }

        .empty{
            text-align:center;
            padding:40px;
            color:#8b949e;
        }

    </style>

</head>

<body>

<div class="header">
    <h1>Riwayat Sinkronisasi</h1>
    <p>Monitoring proses sinkronisasi data ERP POS.</p>
</div>

<div class="container">

    <div class="cards">

        <div class="card">
            <h4>Total Sinkronisasi</h4>
            <h2>{{ $summary['total'] }}</h2>
        </div>

        <div class="card">
            <h4>Berhasil</h4>
            <h2>{{ $summary['success'] }}</h2>
        </div>

        <div class="card">
            <h4>Gagal</h4>
            <h2>{{ $summary['failed'] }}</h2>
        </div>

    </div>

    <form method="GET">

        <div class="search-box">

            <input
                type="text"
                name="search"
                placeholder="Cari module..."
                value="{{ $search }}"
            >

        </div>

    </form>

    @if($histories->count())

    <table>

        <thead>

        <tr>
            <th>No</th>
            <th>Module</th>
            <th>Status</th>
            <th>Pesan</th>
            <th>Waktu Sinkronisasi</th>
        </tr>

        </thead>

        <tbody>

        @foreach($histories as $history)

        <tr>

            <td>{{ $histories->firstItem() + $loop->index }}</td>

            <td>{{ $history->module }}</td>

            <td>

                @if($history->status == 'Berhasil')

                    <span class="badge-success">
                        {{ $history->status }}
                    </span>

                @else

                    <span class="badge-failed">
                        {{ $history->status }}
                    </span>

                @endif

            </td>

            <td>{{ $history->message ?? '-' }}</td>

            <td>{{ \Carbon\Carbon::parse($history->synced_at)->format('d M Y H:i') }}</td>

        </tr>

        @endforeach

        </tbody>

    </table>

    <br>

    {{ $histories->links() }}

    @else

    <div class="empty">

        Belum ada riwayat sinkronisasi.

    </div>

    @endif

</div>

</body>
</html>