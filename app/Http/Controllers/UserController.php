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
            'username' => ['required', 'string', 'max:255', Rule::unique('cashiers', 'username')],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
        ]);

        Cashiers::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with('status', "Pengguna {$validated['username']} berhasil ditambahkan.");
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
