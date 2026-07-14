<?php

namespace App\Services;

use App\Models\Cashiers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginService
{
    public function loginService(string $username, string $password): array
    {
        $cashier = Cashiers::where('username', $username)->first();

        if (!$cashier) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan.',
            ];
        }

        if (!Hash::check($password, $cashier->password)) {
            return [
                'success' => false,
                'message' => 'Password salah.',
            ];
        }

        Session::put('cashier_id', $cashier->id);
        Session::put('cashier_name', $cashier->name);
        Session::put('cashier_username', $cashier->username);
        Session::put('cashier_role', $cashier->role ?? 'kasir');

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => $cashier,
        ];
    }
}