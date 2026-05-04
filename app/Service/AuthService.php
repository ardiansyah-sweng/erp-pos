<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function loginCashier($data)
    {
        // cari user berdasarkan email
        $user = User::where('email', $data['email'])->first();

        // cek user ada atau tidak
        if (!$user) {
            return [
                'success' => false
            ];
        }

        // cek password
        if (!Hash::check($data['password'], $user->password)) {
            return [
                'success' => false
            ];
        }

        // cek role cashier
        if ($user->role !== 'cashier') {
            return [
                'success' => false
            ];
        }

        return [
            'success' => true,
            'data' => $user
        ];
    }
}