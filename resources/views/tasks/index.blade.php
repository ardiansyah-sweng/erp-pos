<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tugas - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-cyan-300">Aktivitas</p>
                <h1 class="mt-2 text-3xl font-bold">Manajemen Tugas</h1>
                <p class="mt-2 text-sm text-slate-400">Catat tugas operasional dan pantau status pengerjaannya.</p>
            </div>
            <a href="{{ route('pos.index') }}" class="rounded-xl border border-white/10 px-4 py-2 text-center text-sm font-semibold hover:border-cyan-400 hover:text-cyan-300">
                Kembali ke POS
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-6 rounded-2xl border border-white/10 bg-white/5 p-5">
            <h2 class="text-lg font-semibold text-white">Tambah Tugas</h2>
            <form method="POST" action="{{ route('tasks.store') }}" class="mt-4 grid gap-3 lg:grid-cols-12">
                @csrf
                <input
                    name="title"
                    value="{{ old('title') }}"
                    required
                    maxlength="150"
                    placeholder="Judul tugas"
                    class="lg:col-span-4 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
                >
                <input
                    name="description"
                    value="{{ old('description') }}"
                    maxlength="1000"
                    placeholder="Deskripsi singkat"
                    class="lg:col-span-5 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400"
                >
                <select name="status" class="lg:col-span-2 rounded-xl border border-white/10 bg-slate-900 px-4 py-3 outline-none focus:border-cyan-400">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', 'pending') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Simpan</button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Tugas</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Dibuat</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($tasks as $task)
                            @php
                                $statusClass = [
                                    'pending' => 'bg-amber-400/15 text-amber-300',
                                    'in_progress' => 'bg-cyan-400/15 text-cyan-300',
                                    'completed' => 'bg-emerald-400/15 text-emerald-300',
                                ][$task->status] ?? 'bg-slate-400/15 text-slate-300';
                            @endphp
                            <tr class="align-top">
                                <td class="px-5 py-5">
                                    <div class="font-semibold text-white">{{ $task->title }}</div>
                                    <div class="mt-1 max-w-2xl text-sm text-slate-400">{{ $task->description ?: 'Tidak ada deskripsi.' }}</div>
                                </td>
                                <td class="px-5 py-5">
                                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClass }}">
                                        {{ $statuses[$task->status] ?? $task->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 text-sm text-slate-400">
                                    {{ $task->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-5 py-5">
                                    <div class="flex min-w-[22rem] flex-col gap-2 sm:flex-row">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="flex flex-1 gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="min-w-0 flex-1 rounded-lg border border-white/10 bg-slate-900 px-3 py-2 text-sm">
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-lg bg-emerald-400 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-300">Update</button>
                                        </form>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full rounded-lg border border-rose-400/40 px-3 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-400/10">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="mt-6">{{ $tasks->links() }}</div>
    </main>
</body>
</html>
