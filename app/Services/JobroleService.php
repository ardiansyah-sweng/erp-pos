<?php

namespace App\Services;

class JobroleService
{
    public function updateCashier($id, array $data)
    {
        return [
            'message' => 'Method updateCashier berhasil dipanggil',
            'id_cashier' => $id,
            'data_baru' => $data
        ];
    }
}