<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ERP POS | @yield('title', 'Dashboard')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>

        body{

            background:#05080f;

            color:white;

            font-family:Inter,sans-serif;

        }

        .nav-link{

            display:flex;

            align-items:center;

            gap:12px;

            padding:10px 14px;

            border-radius:12px;

            color:#94a3b8;

            font-size:14px;

            font-weight:500;

            transition:.15s;

        }

        .nav-link:hover{

            background:rgba(255,255,255,.04);

            color:#e2e8f0;

        }

        .nav-link.active{

            background:linear-gradient(135deg,rgba(34,211,238,.18),rgba(20,184,166,.10));

            color:#22d3ee;

            border:1px solid rgba(34,211,238,.25);

        }

        .nav-section{

            text-transform:uppercase;

            font-size:11px;

            letter-spacing:.12em;

            color:#475569;

            padding:0 14px;

            margin-top:22px;

            margin-bottom:8px;

        }

        .glass{

            background:rgba(15,23,42,.88);

            border:1px solid rgba(255,255,255,.08);

            backdrop-filter:blur(15px);

        }

        .input{

            width:100%;

            border-radius:14px;

            border:1px solid rgba(255,255,255,.08);

            background:#020617;

            padding:13px 16px 13px 44px;

            color:white;

            outline:none;

        }

        .input:focus{

            border-color:#22d3ee;

        }

        .input-icon{

            position:absolute;

            left:14px;

            top:50%;

            transform:translateY(-50%);

            color:#475569;

        }

        .card{

            border-radius:22px;

            border:1px solid rgba(255,255,255,.08);

            background:#0d1424;

        }

        .stat-card{

            border-radius:18px;

            border:1px solid rgba(255,255,255,.08);

            background:#0d1424;

            padding:16px 18px;

            min-width:170px;

        }

    </style>

</head>

<body>

<div class="flex min-h-screen">

    <!-- ===================== SIDEBAR ===================== -->

    <aside class="w-64 fixed inset-y-0 left-0 flex flex-col border-r border-white/5 bg-[#060912] z-30">

        <div class="flex items-center gap-3 px-5 py-6">

            <div class="rounded-xl bg-cyan-500/15 p-2">
                <i data-lucide="box" class="w-6 h-6 text-cyan-400"></i>
            </div>

            <div class="text-lg font-extrabold">
                ERP <span class="text-cyan-400">POS</span>
            </div>

        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-6">

            <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i>
                Dashboard
            </a>

            <a href="{{ url('/pos') }}" class="nav-link {{ request()->is('pos') ? 'active' : '' }}">
                <i data-lucide="shopping-cart" class="w-[18px] h-[18px]"></i>
                <span class="flex-1">Kasir (POS)</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>

            <p class="nav-section">Master Data</p>

            <a href="{{ url('/products') }}" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-[18px] h-[18px]"></i>
                Produk
            </a>

            <a href="{{ url('/categories') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                <i data-lucide="tag" class="w-[18px] h-[18px]"></i>
                Kategori
            </a>

            <a href="{{ route('members.index') }}" class="nav-link {{ request()->is('members*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-[18px] h-[18px]"></i>
                Member
            </a>

            <a href="{{ url('/suppliers') }}" class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                <i data-lucide="truck" class="w-[18px] h-[18px]"></i>
                Supplier
            </a>

            <p class="nav-section">Transaksi</p>

            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->is('transactions') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-[18px] h-[18px]"></i>
                Riwayat Transaksi
            </a>

            <p class="nav-section">Laporan</p>

            <a href="{{ url('/reports/sales') }}" class="nav-link {{ request()->is('reports/sales') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-[18px] h-[18px]"></i>
                Laporan Penjualan
            </a>

            <a href="{{ url('/reports/products') }}" class="nav-link {{ request()->is('reports/products') ? 'active' : '' }}">
                <i data-lucide="pie-chart" class="w-[18px] h-[18px]"></i>
                Laporan Produk
            </a>

            <a href="{{ url('/reports/members') }}" class="nav-link {{ request()->is('reports/members') ? 'active' : '' }}">
                <i data-lucide="user-check" class="w-[18px] h-[18px]"></i>
                Laporan Member
            </a>

            <p class="nav-section">Pengaturan</p>

            <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings') ? 'active' : '' }}">
                <i data-lucide="settings" class="w-[18px] h-[18px]"></i>
                Pengaturan
            </a>

            <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i data-lucide="user-cog" class="w-[18px] h-[18px]"></i>
                User Management
            </a>

        </nav>

        <div class="p-3 border-t border-white/5">

            <form method="POST" action="{{ url('/logout') }}">

                @csrf

                <button type="submit" class="nav-link w-full text-rose-300 hover:bg-rose-500/10 hover:text-rose-200">
                    <i data-lucide="log-out" class="w-[18px] h-[18px]"></i>
                    Keluar
                </button>

            </form>

        </div>

    </aside>

    <!-- ===================== MAIN ===================== -->

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <!-- Topbar -->

        <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-white/5 bg-[#070b14]/95 px-8 py-4 backdrop-blur">

            <div class="flex items-center gap-4">

                <button class="text-slate-400 hover:text-white">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                <div class="font-semibold text-lg">

                    @yield('breadcrumb-prefix', 'Manajemen')
                    <span class="text-cyan-400">@yield('breadcrumb', 'Halaman')</span>

                </div>

            </div>

            <div class="flex items-center gap-6">

                <div class="text-right hidden sm:block">

                    <span id="clockDate" class="text-sm text-slate-300"></span>
                    <span id="clockTime" class="ml-2 text-sm font-bold text-cyan-300"></span>

                </div>

                <button class="text-slate-400 hover:text-white">
                    <i data-lucide="sun" class="w-5 h-5"></i>
                </button>

                <button class="relative text-slate-400 hover:text-white">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-rose-500"></span>
                </button>

                <div class="rounded-full border border-cyan-400/40 p-1.5 text-cyan-300">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>

            </div>

        </header>

        <!-- Page content -->

        <main class="flex-1 p-8">

            @yield('content')

        </main>

    </div>

</div>

<script>

    lucide.createIcons();

    function updateClock(){

        const now = new Date();

        const dateOptions = { weekday:"long", day:"2-digit", month:"long", year:"numeric" };
        const timeOptions = { hour:"2-digit", minute:"2-digit", second:"2-digit" };

        document.getElementById("clockDate").textContent =
            now.toLocaleDateString("id-ID", dateOptions);

        document.getElementById("clockTime").textContent =
            now.toLocaleTimeString("id-ID", timeOptions);

    }

    updateClock();
    setInterval(updateClock, 1000);

</script>

@stack('scripts')

</body>

</html>