<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-4">
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Login
            </button>
        </form>
    </div>

</body>
</html>