<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Cashiers;

class ProfileController extends Controller
{
    /**
     * Ambil data kasir dari session (tanpa Auth facade)
     */
    private function getCashier()
    {
        $id = session('cashier_id');
        if (!$id) return null;
        return Cashiers::find($id);
    }

    /**
     * Menampilkan halaman profil kasir
     */
    public function showProfile()
    {
        $cashier = $this->getCashier() ?? Cashiers::first();

        if (!$cashier) {
            return redirect('/pos');
        }

        return view('profile.index', compact('cashier'));
    }

    /**
     * Update nama dan username kasir
     */
    public function updateProfile(Request $request)
    {
        $cashier = $this->getCashier() ?? Cashiers::first();

        if (!$cashier) {
            return redirect('/pos');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:cashiers,username,' . $cashier->id],
        ], [
            'name.required'     => 'Nama tidak boleh kosong.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.unique'   => 'Username sudah digunakan.',
        ]);

        $cashier->name     = $request->name;
        $cashier->username = $request->username;
        $cashier->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password kasir
     */
    public function updatePassword(Request $request)
    {
        $cashier = $this->getCashier() ?? Cashiers::first();

        if (!$cashier) {
            return redirect('/pos');
        }

        $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama tidak boleh kosong.',
            'new_password.required'     => 'Password baru tidak boleh kosong.',
            'new_password.min'          => 'Password baru minimal 6 karakter.',
            'new_password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $cashier->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $cashier->password = Hash::make($request->new_password);
        $cashier->save();

        return redirect()->route('profile.show')
            ->with('success', 'Password berhasil diperbarui.');
    }
}