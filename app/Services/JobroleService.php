<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class JobroleService
{
    protected string $table = 'cashiers';

    public function updateCashier($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $cashier = DB::table($this->table)
                ->where('id', $id)
                ->first();

            if (!$cashier) {
                return null;
            }
// triger
            DB::table($this->table)
                ->where('id', $id)
                ->update($data);

            return DB::table($this->table)
                ->where('id', $id)
                ->first();
        });
    }
}