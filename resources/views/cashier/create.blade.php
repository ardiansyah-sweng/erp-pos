<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Cashier</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-xl px-4 py-10">
        <div class="rounded-lg bg-white p-6 shadow">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">Tambah Cashier</h1>
                    <p class="mt-1 text-sm text-slate-500">Masukkan data cashier baru.</p>
                </div>
                <a href="{{ route('cashiers.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Kembali</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cashiers.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-blue-500">
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-blue-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-blue-500">
                </div>

                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
                    Simpan Cashier
                </button>
            </form>
        </div>
    </main>
</body>
</html>
