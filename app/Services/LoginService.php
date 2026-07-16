<?php

namespace App\Services;

use App\Models\Cashiers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginService
{
    public function __construct(private HrisService $hrisService)
    {
    }

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

        // Hanya karyawan berstatus kasir aktif di HRIS yang boleh login ke POS.
        if (! $this->hrisService->isCashierEmail($cashier->username)) {
            return [
                'success' => false,
                'message' => 'Akun tidak terdaftar sebagai kasir aktif di HRIS.',
            ];
        }

        Session::put('cashier_id', $cashier->id);
        Session::put('cashier_name', $cashier->name);
        Session::put('cashier_username', $cashier->username);
        Session::put('cashier_role', 'kasir');

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => $cashier,
        ];
    }
}