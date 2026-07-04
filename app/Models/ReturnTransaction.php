<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'return_code',
        'reason',
        'total_refund',
    ];

    protected $casts = [
        'total_refund' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function details()
    {
        return $this->hasMany(ReturnTransactionDetail::class, 'return_transaction_id');
    }
}
