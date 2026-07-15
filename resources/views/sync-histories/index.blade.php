<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sinkronisasi - ERP POS</title>

    <style>
        body{
            font-family:'Segoe UI',sans-serif;
            background:#060b13;
            color:#e2e8f0;
            margin:0;
            padding-bottom:60px;
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
            max-width:1400px;
            margin:auto;
            padding:30px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
            gap:20px;
            margin-bottom:30px;
        }

        .card{
            background:#0b111e;
            border:1px solid #1e293b;
            border-radius:16px;
            padding:24px;
            position:relative;
            overflow:hidden;
        }

        .card::before {
            content:'';
            position:absolute;
            top:0;
            left:0;
            width:4px;
            height:100%;
            background:linear-gradient(to bottom, #06b6d4, #0ea5e9);
        }

        .card h4{
            margin:0;
            color:#94a3b8;
            font-size:14px;
            text-transform:uppercase;
        }

        .card h2{
            margin:12px 0 0;
            font-size:32px;
            color:#ffffff;
        }

        .search-box{
            margin-bottom:25px;
        }

        .search-box input{
            width:100%;
            max-width:360px;
            padding:12px 16px;
            background:#0b111e;
            border:1px solid #1e293b;
            color:#f8fafc;
            border-radius:10px;
            font-size:14px;
        }

        .search-box input:focus{
            outline:none;
            border-color:#0ea5e9;
        }

        .table-responsive{
            width:100%;
            overflow-x:auto;
            background:#0b111e;
            border:1px solid #1e293b;
            border-radius:14px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            padding:16px 20px;
            border-bottom:1px solid #1e293b;
            font-size:14px;
        }

        th{
            background:#0f172a;
            color:#94a3b8;
            text-align:left;
            font-size:12px;
            text-transform:uppercase;
        }

        tr:last-child td{
            border-bottom:none;
        }

        tr:hover td{
            background:rgba(30,41,59,0.3);
        }

        .badge-success{
            background:rgba(16,185,129,0.15);
            color:#10b981;
            padding:6px 14px;
            border-radius:8px;
            font-size:12px;
            font-weight:600;
            display:inline-block;
        }

        .empty{
            text-align:center;
            padding:60px 20px;
            color:#64748b;
            background:#0b111e;
            border:1px solid #1e293b;
            border-radius:14px;
        }

        .pagination-wrapper{
            margin-top:25px;
            display:flex;
            justify-content:center;
            align-items:center;
            gap:15px;
        }

        .pagination-wrapper a,
        .pagination-wrapper span{
            padding:10px 16px;
            border-radius:8px;
            border:1px solid #1e293b;
            background:#0b111e;
            color:#e2e8f0;
            text-decoration:none;
            font-size:14px;
        }

        .pagination-wrapper a:hover{
            background:#1e293b;
        }

        .pagination-wrapper .disabled{
            color:#64748b;
        }

        .page-info{
            color:#94a3b8;
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

    <div class="table-responsive">

        <table>

            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Module</th>
                    <th>Status</th>
                    <th>Pesan</th>
                    <th>Waktu Sinkronisasi</th>
                </tr>
            </thead>


            <tbody>

                @foreach($histories as $history)

                <tr>

                    <td>
                        {{ $histories->firstItem() + $loop->index }}
                    </td>

                    <td style="font-weight:500;">
                        {{ $history->module }}
                    </td>

                    <td>
                        <span class="badge-success">
                            {{ $history->status }}
                        </span>
                    </td>

                    <td style="color:#cbd5e1;">
                        {{ $history->message ?? '-' }}
                    </td>

                    <td style="color:#94a3b8;">
                        {{ \Carbon\Carbon::parse($history->synced_at)->format('d M Y H:i') }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>


    <div class="pagination-wrapper">

        @if($histories->onFirstPage())

            <span class="disabled">
                ← Sebelumnya
            </span>

        @else

            <a href="{{ $histories->previousPageUrl() }}">
                ← Sebelumnya
            </a>

        @endif


        <span class="page-info">
            Halaman {{ $histories->currentPage() }}
            dari {{ $histories->lastPage() }}
        </span>


        @if($histories->hasMorePages())

            <a href="{{ $histories->nextPageUrl() }}">
                Selanjutnya →
            </a>

        @else

            <span class="disabled">
                Selanjutnya →
            </span>

        @endif

    </div>


    @else

    <div class="empty">
        Belum ada riwayat sinkronisasi.
    </div>

    @endif


</div>

</body>
</html>