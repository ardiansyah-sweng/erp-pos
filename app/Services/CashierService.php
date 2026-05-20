<?php

namespace App\Services;

use App\Models\Cashiers;

class CashierService
{
    public function getAllCashier()
    {
        return Cashiers::orderBy('name')->get();
    }
}