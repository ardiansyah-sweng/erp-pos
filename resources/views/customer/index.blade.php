<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelanggan - ERP POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto py-8 px-4">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Data Pelanggan</h1>
            <a href="{{ route('pos.index') }}" class="text-sm text-blue-400 hover:text-blue-300">← Kembali ke POS</a>
        </div>

        @if (session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="bg-red-600 text-white px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-red-600 text-white px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white/10 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Tambah Pelanggan Baru</h2>
            <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1">No. Telepon</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1">Email (opsional)</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="email@example.com">
                    </div>
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium mb-1">Alamat (opsional)</label>
                    <textarea id="address" name="address" rows="2"
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                </div>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-medium transition-colors">
                    Simpan
                </button>
            </form>
        </div>

        <div class="bg-white/10 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white/5 text-left">
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Telepon</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Poin</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($customers as $index => $customer)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $customer->email ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $customer->loyalty_points }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $customer->is_active ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400' }}">
                                    {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-email="{{ $customer->email }}" data-phone="{{ $customer->phone }}" data-address="{{ $customer->address }}" data-active="{{ $customer->is_active }}" onclick="openEdit(this)"
                                        class="text-blue-400 hover:text-blue-300 text-xs">Edit</button>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan {{ $customer->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Edit Pelanggan</h3>
                <button onclick="closeEdit()" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" id="edit_name" name="name" required
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_phone" class="block text-sm font-medium mb-1">No. Telepon</label>
                    <input type="text" id="edit_phone" name="phone"
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_email" class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="edit_email" name="email"
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_address" class="block text-sm font-medium mb-1">Alamat</label>
                    <textarea id="edit_address" name="address" rows="2"
                        class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded">
                    <label for="edit_is_active" class="text-sm">Aktif</label>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEdit()"
                        class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded text-sm transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-sm font-medium transition-colors">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');

        function openEdit(btn) {
            const id = btn.dataset.id;
            editForm.action = '/customers/' + id;
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_phone').value = btn.dataset.phone || '';
            document.getElementById('edit_email').value = btn.dataset.email || '';
            document.getElementById('edit_address').value = btn.dataset.address || '';
            document.getElementById('edit_is_active').checked = btn.dataset.active === '1';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEdit() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeEdit();
        });
    </script>
</body>
</html>