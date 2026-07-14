<?php

namespace App\Http\Controllers;

use App\Models\Cashiers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = Cashiers::orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('cashiers', 'username')],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
        ]);

        // Email disimpan pada kolom "username" (identitas login), skema tidak diubah.
        Cashiers::create([
            'name' => $validated['name'],
            'username' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with('status', "Pengguna {$validated['email']} berhasil ditambahkan.");
    }

    public function destroy(Request $request, Cashiers $cashier): RedirectResponse
    {
        if ($cashier->id === $request->session()->get('cashier_id')) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $cashier->delete();

        return back()->with('status', "Pengguna {$cashier->username} berhasil dihapus.");
    }
}
