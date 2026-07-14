<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
        } catch (error) {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>

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

        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .nav-section,
        .sidebar-collapsed .logo-text {
            display: none;
        }

        /* ============= LIGHT MODE ============= */
        html[data-theme="light"] body {
            background: #f6f8fb !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] .bg-white\/5 {
            background-color: rgba(255, 255, 255, 0.94) !important;
        }

        html[data-theme="light"] .bg-white\/10 {
            background-color: #f1f5f9 !important;
        }

        html[data-theme="light"] .border-white\/5,
        html[data-theme="light"] .border-white\/10 {
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .text-slate-400,
        html[data-theme="light"] .text-slate-300,
        html[data-theme="light"] .text-slate-500 {
            color: #64748b !important;
        }

        html[data-theme="light"] .text-white {
            color: #0f172a !important;
        }

        html[data-theme="light"] .text-rose-300 {
            color: #e11d48 !important;
        }

        html[data-theme="light"] .text-cyan-400 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] .text-cyan-300 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] input,
        html[data-theme="light"] select,
        html[data-theme="light"] textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] input::placeholder,
        html[data-theme="light"] textarea::placeholder {
            color: #94a3b8 !important;
        }

        html[data-theme="light"] .nav-link {
            color: #64748b !important;
        }

        html[data-theme="light"] .nav-link:hover {
            background: rgba(0, 0, 0, 0.04) !important;
            color: #334155 !important;
        }

        html[data-theme="light"] .nav-link.active {
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.18), rgba(20, 184, 166, 0.10)) !important;
            color: #0e7490 !important;
            border-color: rgba(34, 211, 238, 0.4) !important;
        }

        html[data-theme="light"] .nav-section {
            color: #94a3b8 !important;
        }

        html[data-theme="light"] aside[id="sidebar"] {
            background-color: #ffffff !important;
        }

        html[data-theme="light"] header {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }

        html[data-theme="light"] .glass {
            background: rgba(255, 255, 255, 0.88) !important;
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .input {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .card {
            background: #ffffff !important;
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .stat-card {
            background: #ffffff !important;
            border-color: #dbe3ea !important;
        }

    </style>

    @stack('styles')

</head>

<body>

<div class="flex min-h-screen">

    <!-- ===================== SIDEBAR ===================== -->

    <aside id="sidebar" class="w-64 fixed inset-y-0 left-0 flex flex-col border-r border-white/5 bg-[#060912] z-30 transition-all duration-300">

        <div class="flex items-center gap-3 px-5 py-6">

            <div class="rounded-xl bg-cyan-500/15 p-2">
                <i data-lucide="box" class="w-6 h-6 text-cyan-400"></i>
            </div>

            <div class="text-lg font-extrabold logo-text">
                ERP <span class="text-cyan-400">POS</span>
            </div>

        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-6">

            <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <a href="{{ url('/pos') }}" class="nav-link {{ request()->is('pos') ? 'active' : '' }}">
                <i data-lucide="shopping-cart" class="w-[18px] h-[18px]"></i>
                    <span class="flex-1 nav-text">Kasir (POS)</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 nav-text"></i>
            </a>

            <p class="nav-section nav-text">Master Data</p>

            <a href="{{ route('products.manage') }}" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                <i data-lucide="package" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Produk</span>
            </a>

            <a href="{{ route('stock-adjustments.index') }}" class="nav-link {{ request()->is('stock-adjustments*') ? 'active' : '' }}">
                <i data-lucide="archive" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Penyesuaian Stok</span>
            </a>

            <a href="{{ url('/categories') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                <i data-lucide="tag" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Kategori</span>
            </a>

            <a href="{{ route('members.index') }}" class="nav-link {{ request()->is('members*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Member</span>
            </a>

            <a href="{{ url('/suppliers') }}" class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                <i data-lucide="truck" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Supplier</span>
            </a>

            <a href="{{ route('discounts.index') }}" class="nav-link {{ request()->is('discounts*') ? 'active' : '' }}">
                <i data-lucide="percent" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Diskon</span>
            </a>

            <p class="nav-section nav-text">Transaksi</p>

            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->is('transactions') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Riwayat Transaksi</span>
            </a>

            <a href="{{ route('returns.index') }}" class="nav-link {{ request()->is('returns*') ? 'active' : '' }}">
                <i data-lucide="undo-2" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Retur Transaksi</span>
            </a>

            <p class="nav-section nav-text">Laporan</p>

            <a href="{{ route('sales-notes') }}" class="nav-link {{ request()->is('sales-notes*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Laporan Penjualan</span>
            </a>

            <a href="{{ url('/reports/products') }}" class="nav-link {{ request()->is('reports/products') ? 'active' : '' }}">
                <i data-lucide="pie-chart" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Laporan Produk</span>
            </a>

            <a href="{{ url('/reports/members') }}" class="nav-link {{ request()->is('reports/members') ? 'active' : '' }}">
                <i data-lucide="user-check" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Laporan Member</span>
            </a>

            <p class="nav-section nav-text">Pengaturan</p>

            <a href="{{ url('/settings') }}" class="nav-link {{ request()->is('settings') ? 'active' : '' }}">
                <i data-lucide="settings" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">Pengaturan</span>
            </a>

            <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i data-lucide="user-cog" class="w-[18px] h-[18px]"></i>
                <span class="nav-text">User Management</span>
            </a>

        </nav>

        <div class="p-3 border-t border-white/5 space-y-1">

            <button id="sidebarThemeToggle" type="button" class="nav-link w-full text-slate-400 hover:text-white">
                <span id="sidebarThemeIcon" class="inline-flex w-[18px] h-[18px] items-center justify-center"></span>
                <span id="sidebarThemeLabel" class="nav-text">Mode terang</span>
            </button>

            <form method="POST" action="{{ url('/logout') }}">

                @csrf

                <button type="submit" class="nav-link w-full text-rose-300 hover:bg-rose-500/10 hover:text-rose-200">
                    <i data-lucide="log-out" class="w-[18px] h-[18px]"></i>
                    <span class="nav-text">Keluar</span>
                </button>

            </form>

        </div>

    </aside>

    <!-- ===================== MAIN ===================== -->

    <div id="mainContent" class="flex-1 ml-64 flex flex-col min-h-screen transition-all duration-300">

        <!-- Topbar -->

        <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-white/5 bg-[#070b14]/95 px-8 py-4 backdrop-blur">

            <div class="flex items-center gap-4">

                <button id="sidebarToggle" class="text-slate-400 hover:text-white">
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

                <button id="topbarThemeToggle" class="text-slate-400 hover:text-white">
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

        <div class="flex-1">

            @yield('content')

        </div>

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

    // Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');

    function toggleSidebar() {
        sidebar.classList.toggle('sidebar-collapsed');
        const collapsed = sidebar.classList.contains('sidebar-collapsed');
        if (collapsed) {
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            mainContent.classList.remove('ml-64');
            mainContent.classList.add('ml-20');
        } else {
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            mainContent.classList.remove('ml-20');
            mainContent.classList.add('ml-64');
        }
        localStorage.setItem('sidebarCollapsed', collapsed);
    }

    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        toggleSidebar();
    }

    sidebarToggle.addEventListener('click', toggleSidebar);

    // Theme toggle
    const themeStorageKey = 'erp-pos-theme';
    const sidebarThemeToggle = document.getElementById('sidebarThemeToggle');
    const sidebarThemeIcon = document.getElementById('sidebarThemeIcon');
    const sidebarThemeLabel = document.getElementById('sidebarThemeLabel');
    const topbarThemeToggle = document.getElementById('topbarThemeToggle');

    const sidebarIcons = {
        sun: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[18px] h-[18px]"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>',
        moon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[18px] h-[18px]"><path d="M12 3a6 6 0 0 0 9 7.5A9 9 0 1 1 12 3Z"></path></svg>',
    };

    const topbarIcons = {
        sun: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>',
        moon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M12 3a6 6 0 0 0 9 7.5A9 9 0 1 1 12 3Z"></path></svg>',
    };

    function applyTheme(theme) {
        document.documentElement.dataset.theme = theme;
        const isLight = theme === 'light';
        sidebarThemeIcon.innerHTML = isLight ? sidebarIcons.moon : sidebarIcons.sun;
        sidebarThemeLabel.textContent = isLight ? 'Mode gelap' : 'Mode terang';
        if (topbarThemeToggle) {
            topbarThemeToggle.innerHTML = isLight ? topbarIcons.moon : topbarIcons.sun;
        }
        try { localStorage.setItem(themeStorageKey, theme); } catch (e) {}
    }

    function toggleTheme() {
        const current = document.documentElement.dataset.theme || 'dark';
        applyTheme(current === 'light' ? 'dark' : 'light');
    }

    const savedTheme = localStorage.getItem(themeStorageKey) || 'dark';
    applyTheme(savedTheme);

    sidebarThemeToggle.addEventListener('click', toggleTheme);
    if (topbarThemeToggle) {
        topbarThemeToggle.addEventListener('click', toggleTheme);
    }

</script>

@stack('scripts')

</body>

</html>