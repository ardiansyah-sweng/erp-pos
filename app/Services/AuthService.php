<?php

namespace App\Services;

use App\Models\Cashier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function loginCashier(string $username, string $password): array
    {
        $cashier = Cashier::where('username', $username)->first();

        if (! $cashier || ! Hash::check($password, $cashier->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $token = $cashier->createToken('cashier-token')->plainTextToken;

        return [
            'cashier' => [
                'id'       => $cashier->id,
                'name'     => $cashier->name,
                'username' => $cashier->username,
            ],
            'token' => $token,
        ];
    }
}