<?php

namespace App\Services;

use App\Models\Cashier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginService
{
    /**
     * Handle user login authentication logic
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public function loginService(string $username, string $password): array
    {
        // Cari user berdasarkan username
        $cashier = Cashier::where('username', $username)->first();

        // Validasi: user tidak ditemukan
        if (!$cashier) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan.',
            ];
        }

        // Validasi: password salah
        if (!Hash::check($password, $cashier->password)) {
            return [
                'success' => false,
                'message' => 'Password salah.',
            ];
        }

        // Simpan data session setelah login berhasil
        Session::put('cashier_id', $cashier->id);
        Session::put('cashier_name', $cashier->name);
        Session::put('cashier_username', $cashier->username);

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => $cashier,
        ];
    }
}