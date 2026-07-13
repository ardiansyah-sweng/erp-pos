<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CashierController extends Controller
{
    /**
     * Tampilkan daftar semua kasir
     */
    public function index()
    {
        $cashiers = Cashiers::orderBy('name')->get();

        return view('cashier.index', compact('cashiers'));
    }

    /**
     * Cek ketersediaan username (untuk real-time AJAX check)
     */
    public function checkUsername(Request $request)
    {
        $username  = trim($request->get('username', ''));
        $excludeId = $request->get('exclude_id');

        if (empty($username)) {
            return response()->json(['available' => null]);
        }

        $query = Cashiers::where('username', $username);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $taken = $query->exists();

        return response()->json([
            'available' => !$taken,
            'message'   => $taken ? 'Username sudah digunakan.' : 'Username tersedia.',
        ]);
    }

    /**
     * Tambah kasir baru
     */
    public function add(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:cashiers,username'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'name.required'      => 'Nama tidak boleh kosong.',
            'username.required'  => 'Username tidak boleh kosong.',
            'username.unique'    => 'Username sudah digunakan.',
            'password.required'  => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        Cashiers::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('cashier.index')
            ->with('success', 'Kasir berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit kasir
     */
    public function edit(int $id)
    {
        $cashier = Cashiers::findOrFail($id);

        return view('cashier.edit', compact('cashier'));
    }

    /**
     * Simpan perubahan data kasir
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:cashiers,username,' . $id],
        ], [
            'name.required'     => 'Nama tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.unique'   => 'Username sudah digunakan.',
        ]);

        $cashier = Cashiers::findOrFail($id);
        $cashier->update([
            'name'     => $request->name,
            'username' => $request->username,
        ]);

        return redirect()->route('cashier.index')
            ->with('success', 'Data kasir berhasil diperbarui.');
    }
}