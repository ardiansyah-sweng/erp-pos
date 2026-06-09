<?php

namespace App\Services;

use App\Models\Cashiers;

class CashierService
{
    public function findCashier($keyword)
    {
        return Cashier::query()->search($keyword)->get();
    }
}