<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | ERP POS</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('erp-pos-theme') || 'dark';
        } catch (error) {
            document.documentElement.dataset.theme = 'dark';
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html[data-theme="light"] body {
            background: #f6f8fb !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] body > .absolute {
            opacity: 0.45;
        }

        html[data-theme="light"] .bg-white\/5 {
            background-color: rgba(255, 255, 255, 0.94) !important;
        }

        html[data-theme="light"] .bg-slate-950\/70 {
            background-color: #ffffff !important;
        }

        html[data-theme="light"] .border-white\/10 {
            border-color: #dbe3ea !important;
        }

        html[data-theme="light"] .text-white {
            color: #0f172a !important;
        }

        html[data-theme="light"] .text-slate-300,
        html[data-theme="light"] .text-slate-400 {
            color: #64748b !important;
        }

        html[data-theme="light"] .text-rose-300 {
            color: #e11d48 !important;
        }

        html[data-theme="light"] .text-cyan-300\/80 {
            color: #0e7490 !important;
        }

        html[data-theme="light"] input {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] input::placeholder {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-emerald-500/30 via-cyan-500/20 to-transparent blur-3xl"></div>

    <main class="relative flex min-h-screen items-center justify-center px-4 py-6">
        <section class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl">
            <div class="text-center">
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300/80">ERP POS</p>
                <h1 class="mt-2 text-2xl font-semibold text-white">Login Kasir</h1>
                <p class="mt-2 text-sm text-slate-300">Masuk dengan username dan password kasir kamu.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-300">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mt-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form class="mt-6 space-y-4" action="{{ route('login.attempt') }}" method="POST">
                @csrf

                <div>
                    <label for="username" class="text-sm text-slate-300">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        autocomplete="username"
                        required
                        autofocus
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400"
                    >
                </div>

                <div>
                    <label for="password" class="text-sm text-slate-300">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        placeholder="Masukkan password"
                        class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-white outline-none placeholder:text-slate-500 focus:border-cyan-400"
                    >
                </div>

                <button
                    type="submit"
                    class="mt-2 w-full rounded-xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300"
                >
                    Masuk
                </button>
            </form>
        </section>
    </main>
</body>
</html>
