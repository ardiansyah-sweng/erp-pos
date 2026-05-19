<?php

namespace App\Services;

use App\Models\Cashier;

class CashierService
{
    public function findCashier($keyword)
    {
        return Cashier::search($keyword)->get();
    }
}