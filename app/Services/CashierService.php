<?php

namespace App\Services;

use App\Models\Cashier;

class CashierService
{
    public function getAllCashier()
    {
        return Cashier::all();
    }
}