<?php

namespace App\Services;

use App\Models\Cashiers;
use Illuminate\Support\Facades\Hash;

class CashierService
{
    /**
     * Ambil semua data kasir, diurutkan by name
     */
    public function getAllCashier()
    {
        return Cashiers::orderBy('name')->get();
    }

    /**
     * Tambah kasir baru
     */
    public function createCashier(array $data): Cashiers
    {
        return Cashiers::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Update data kasir (nama & username saja)
     */
    public function updateCashier(int $id, array $data): Cashiers
    {
        $cashier = Cashiers::findOrFail($id);
        $cashier->update([
            'name'     => $data['name'],
            'username' => $data['username'],
        ]);

        return $cashier->refresh();
    }
}