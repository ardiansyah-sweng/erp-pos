<?php

namespace App\Services;

use App\Models\Cashier;
use Illuminate\Support\Facades\DB;

class JobroleService
{
    /**
     * Update data cashier berdasarkan ID
     *
     * @param int $id
     * @param array $data
     * @return Cashier|null
     * @throws \Exception
     */
    public function updateCashier($id, array $data)
    {
        DB::beginTransaction();

        try {
            // Cari data cashier berdasarkan ID
            $cashier = Cashier::find($id);

            // Jika tidak ditemukan
            if (!$cashier) {
                throw new \Exception("Cashier tidak ditemukan");
            }

            // Update data cashier
            $cashier->update($data);

            // Commit jika berhasil
            DB::commit();

            // Return data yang sudah diupdate
            return $cashier;

        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            throw $e;
        }
    }
}