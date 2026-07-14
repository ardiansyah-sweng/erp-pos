<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $fillable = ['total', 'transaction_date', 'created_at', 'updated_at', 'customer_id'];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentDetail::class, 'transaction_id');
    }

    public function returns()
    {
        return $this->hasMany(ReturnTransaction::class, 'transaction_id');
    }

    /**
     * Ambil nama kolom tertentu dari $fillable
     */
    public function getColumn($index)
    {
        $fillable = $this->getFillable();

        return $fillable[$index] ?? null;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
