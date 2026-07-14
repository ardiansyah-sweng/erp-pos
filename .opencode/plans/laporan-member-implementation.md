# Implementasi Laporan Member (`/reports/members`)

## 1. Route — `routes/web.php`

Tambah baris setelah `Route::get('/reports', ...)`:

```php
Route::get('/reports/members', [ReportController::class, 'memberReport'])->name('reports.members');
```

---

## 2. Controller — `app/Http/Controllers/ReportController.php`

Tambah method baru setelah method `index()`:

```php
use App\Models\Customer;
use Carbon\Carbon;

public function memberReport(Request $request)
{
    $level = $request->get('level', '');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // ── Summary Cards ──
    $totalMembers = Customer::count();
    $newThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();
    $totalPoints = Customer::sum('points');
    $avgSpending = Customer::join('transaction', 'customers.id', '=', 'transaction.customer_id')
        ->avg('transaction.total') ?? 0;

    // ── Distribusi Level (Pie) ──
    $levelDistribution = Customer::selectRaw('member_level, COUNT(*) as total')
        ->groupBy('member_level')
        ->orderBy('member_level')
        ->get();

    // ── Pertumbuhan Member per Bulan (6 bulan) ──
    $monthlyGrowth = Customer::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as bulan,
            COUNT(*) as total
        ")
        ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();

    // ── Filter level & tanggal untuk tabel ──
    $memberQuery = Customer::withCount('transactions')
        ->withSum('transactions', 'total')
        ->orderBy('transactions_sum_total', 'desc');

    if ($level) {
        $memberQuery->where('member_level', $level);
    }

    // ── Top 10 Member by Total Belanja ──
    $topSpenders = Customer::withSum('transactions', 'total')
        ->withCount('transactions')
        ->having('transactions_sum_total', '>', 0)
        ->orderBy('transactions_sum_total', 'desc')
        ->limit(10)
        ->get();

    // ── Top 10 Member by Poin ──
    $topPoints = Customer::orderBy('points', 'desc')
        ->limit(10)
        ->get();

    // ── Top 10 Member by Jumlah Transaksi ──
    $topFrequent = Customer::withCount('transactions')
        ->having('transactions_count', '>', 0)
        ->orderBy('transactions_count', 'desc')
        ->limit(10)
        ->get();

    // ── Semua member untuk tabel ──
    $members = $memberQuery->paginate(20)->withQueryString();

    return view('reports.members', compact(
        'totalMembers',
        'newThisMonth',
        'totalPoints',
        'avgSpending',
        'levelDistribution',
        'monthlyGrowth',
        'topSpenders',
        'topPoints',
        'topFrequent',
        'members',
        'level',
        'startDate',
        'endDate'
    ));
}
```

### Update `use` statements di bagian atas file

Tambahkan:
```php
use App\Models\Customer;
```

---

## 3. View — `resources/views/reports/members.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Laporan Member')
@section('breadcrumb-prefix', 'Laporan')
@section('breadcrumb', 'Member')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card {
        border-radius: 18px;
        border: 1px solid rgba(255,255,255,.08);
        background: #0d1424;
        padding: 16px 18px;
        min-width: 170px;
    }
    html[data-theme="light"] .stat-card {
        background: #ffffff !important;
        border-color: #dbe3ea !important;
    }
    html[data-theme="light"] .stat-card .text-slate-400 { color: #64748b !important; }
    html[data-theme="light"] .stat-card .text-white { color: #0f172a !important; }
    .flatpickr-calendar { background: #161b22 !important; border: 1px solid #21262d !important; }
    .flatpickr-months .flatpickr-month,
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-weekdays { background: #161b22 !important; color: #e6edf3 !important; }
    .flatpickr-weekday { color: #8b949e !important; }
    .flatpickr-day { color: #e6edf3 !important; }
    .flatpickr-day:hover { background: #21262d !important; }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover { background: #2dd4bf !important; border-color: #2dd4bf !important; color: #000 !important; }
    .flatpickr-day.today { border-color: #2dd4bf !important; }
    .flatpickr-input { background: #020617 !important; color: white !important; border: 1px solid rgba(255,255,255,.08) !important; border-radius: 14px !important; padding: 10px 14px !important; }
    html[data-theme="light"] .flatpickr-input { background: #ffffff !important; color: #0f172a !important; border-color: #dbe3ea !important; }
    .table-header { background: rgba(34,211,238,.08); }
    .pagination nav span { color: #94a3b8; }
    .pagination nav a { color: #22d3ee; }
</style>
@endpush

@section('content')
<div class="p-8">

    {{-- HEADER BANNER --}}
    <div class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-8 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)]">
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-cyan-400">ERP POS</p>
                <h1 class="mt-1 text-4xl font-extrabold text-white">Laporan Member</h1>
                <p class="mt-2 text-slate-400">Analisis data member dan loyalitas pelanggan.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm text-slate-300">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="text-3xl font-bold text-cyan-300" id="liveClock">--:--:--</p>
                </div>
                <a href="{{ route('members.index') }}" class="flex items-center gap-2 rounded-xl border border-cyan-500/40 px-5 py-3 font-semibold text-cyan-300 hover:bg-cyan-500/10 transition">
                    <i data-lucide="users" class="w-[18px] h-[18px]"></i>
                    Kelola Member
                </a>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-cyan-500/15 p-2.5">
                    <i data-lucide="users" class="w-5 h-5 text-cyan-300"></i>
                </div>
                <p class="text-slate-400">Total Member</p>
            </div>
            <h2 class="mt-3 text-3xl font-bold text-white">{{ number_format($totalMembers) }}</h2>
            <p class="mt-1 text-xs text-slate-400">Semua member terdaftar</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-emerald-500/15 p-2.5">
                    <i data-lucide="user-plus" class="w-5 h-5 text-emerald-300"></i>
                </div>
                <p class="text-slate-400">Member Baru</p>
            </div>
            <h2 class="mt-3 text-3xl font-bold text-white">{{ number_format($newThisMonth) }}</h2>
            <p class="mt-1 text-xs text-slate-400">Bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-amber-500/15 p-2.5">
                    <i data-lucide="award" class="w-5 h-5 text-amber-300"></i>
                </div>
                <p class="text-slate-400">Total Poin</p>
            </div>
            <h2 class="mt-3 text-3xl font-bold text-white">{{ number_format($totalPoints) }}</h2>
            <p class="mt-1 text-xs text-slate-400">Poin aktif member</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-rose-500/15 p-2.5">
                    <i data-lucide="trending-up" class="w-5 h-5 text-rose-300"></i>
                </div>
                <p class="text-slate-400">Rata-rata Belanja</p>
            </div>
            <h2 class="mt-3 text-3xl font-bold text-white">Rp {{ number_format((int) $avgSpending) }}</h2>
            <p class="mt-1 text-xs text-slate-400">Per member</p>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Pie: Distribusi Level --}}
        <div class="card p-6">
            <h3 class="text-lg font-bold text-white mb-4">Distribusi Level Member</h3>
            <canvas id="levelChart" height="200"></canvas>
        </div>

        {{-- Bar: Pertumbuhan Member --}}
        <div class="card p-6">
            <h3 class="text-lg font-bold text-white mb-4">Pertumbuhan Member (6 Bulan)</h3>
            <canvas id="growthChart" height="200"></canvas>
        </div>
    </div>

    {{-- TOP LISTS SECTION --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Top Belanja --}}
        <div class="card p-5">
            <h3 class="text-sm font-bold text-cyan-400 mb-3">Top 10 by Belanja</h3>
            <div class="space-y-2">
                @forelse($topSpenders as $i => $m)
                <div class="flex items-center justify-between py-1.5 {{ $i > 0 ? 'border-t border-white/5' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-cyan-300/60 w-5">#{{ $i + 1 }}</span>
                        <span class="text-sm text-white">{{ $m->name }}</span>
                    </div>
                    <span class="text-sm font-semibold text-emerald-300">Rp {{ number_format((int) ($m->transactions_sum_total ?? 0)) }}</span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada data transaksi</p>
                @endforelse
            </div>
        </div>

        {{-- Top Poin --}}
        <div class="card p-5">
            <h3 class="text-sm font-bold text-amber-400 mb-3">Top 10 by Poin</h3>
            <div class="space-y-2">
                @forelse($topPoints as $i => $m)
                <div class="flex items-center justify-between py-1.5 {{ $i > 0 ? 'border-t border-white/5' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-300/60 w-5">#{{ $i + 1 }}</span>
                        <span class="text-sm text-white">{{ $m->name }}</span>
                    </div>
                    <span class="text-sm font-semibold text-amber-300">{{ number_format($m->points) }} pts</span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada poin</p>
                @endforelse
            </div>
        </div>

        {{-- Top Frekuensi --}}
        <div class="card p-5">
            <h3 class="text-sm font-bold text-rose-400 mb-3">Top 10 by Transaksi</h3>
            <div class="space-y-2">
                @forelse($topFrequent as $i => $m)
                <div class="flex items-center justify-between py-1.5 {{ $i > 0 ? 'border-t border-white/5' : '' }}">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-rose-300/60 w-5">#{{ $i + 1 }}</span>
                        <span class="text-sm text-white">{{ $m->name }}</span>
                    </div>
                    <span class="text-sm font-semibold text-rose-300">{{ $m->transactions_count }}x</span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="mt-6 card p-5">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Level Member</label>
                <select name="level" class="input !pl-4 !pr-8" style="width:180px">
                    <option value="">Semua Level</option>
                    @foreach(['Regular','Silver','Gold','Platinum'] as $l)
                    <option value="{{ $l }}" {{ $level === $l ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="rounded-xl bg-cyan-400 px-5 py-2.5 font-semibold text-slate-950 hover:bg-cyan-300 transition text-sm">
                    Terapkan Filter
                </button>
            </div>
            @if($level)
            <div>
                <a href="{{ route('reports.members') }}" class="rounded-xl border border-white/10 px-5 py-2.5 text-sm text-slate-300 hover:border-cyan-400/50 transition">
                    Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- TABLE --}}
    <div class="mt-4 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="table-header">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Level</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">No. HP</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Total Transaksi</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Total Belanja</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-cyan-300 uppercase tracking-wider">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($members as $m)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-slate-400 font-mono">{{ $m->customer_code }}</td>
                        <td class="px-4 py-3 text-white font-medium">{{ $m->name }}</td>
                        <td class="px-4 py-3">
                            @php
                                $colors = ['Regular' => 'text-slate-400', 'Silver' => 'text-slate-300', 'Gold' => 'text-amber-400', 'Platinum' => 'text-cyan-300'];
                            @endphp
                            <span class="{{ $colors[$m->member_level] ?? 'text-slate-400' }}">{{ $m->member_level }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">{{ $m->phone }}</td>
                        <td class="px-4 py-3 text-right text-white">{{ $m->transactions_count ?? 0 }}x</td>
                        <td class="px-4 py-3 text-right text-emerald-300 font-medium">Rp {{ number_format((int) ($m->transactions_sum_total ?? 0)) }}</td>
                        <td class="px-4 py-3 text-right text-amber-300">{{ number_format($m->points) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada data member</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
        <div class="px-4 py-3 border-t border-white/5 pagination">
            {{ $members->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── Distribusi Level (Pie) ──
    const levelCtx = document.getElementById('levelChart').getContext('2d');
    new Chart(levelCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($levelDistribution->pluck('member_level')) !!},
            datasets: [{
                data: {!! json_encode($levelDistribution->pluck('total')->map(fn($v) => (int) $v)) !!},
                backgroundColor: ['#64748b', '#cbd5e1', '#fbbf24', '#22d3ee'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8', padding: 16, usePointStyle: true }
                }
            }
        }
    });

    // ── Pertumbuhan Member (Bar) ──
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyGrowth->pluck('bulan')->map(fn($b) => \Carbon\Carbon::parse($b.'-01')->translatedFormat('M Y'))) !!},
            datasets: [{
                label: 'Member Baru',
                data: {!! json_encode($monthlyGrowth->pluck('total')->map(fn($v) => (int) $v)) !!},
                backgroundColor: 'rgba(34, 211, 238, 0.5)',
                borderColor: '#22d3ee',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: { color: '#64748b' },
                    grid: { color: 'rgba(255,255,255,0.03)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#64748b', stepSize: 1 },
                    grid: { color: 'rgba(255,255,255,0.03)' }
                }
            }
        }
    });
</script>
<script>
    // Live clock
    function updateClock(){
        const now = new Date();
        document.getElementById('liveClock').textContent = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@endpush
```

---

## 4. Urutan Implementasi

1. Edit `routes/web.php` — tambah 1 baris route
2. Edit `app/Http/Controllers/ReportController.php` — tambah `use App\Models\Customer;` + method `memberReport()`
3. Buat `resources/views/reports/members.blade.php` — isi dengan kode view di atas
4. Jalankan `php artisan route:list | findstr members` — verifikasi route terdaftar
5. Akses `http://127.0.0.1:8000/reports/members` — verifikasi halaman tampil
6. Jalankan `php artisan test --filter Report` — jika ada test terkait
