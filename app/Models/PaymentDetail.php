<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'payment_method',
        'amount',
        'reference_number',
        'payment_status',
        'discount_amount',
        'cash_tendered',
        'change_amount',
    ];

    protected $casts = [
        'amount' => 'integer',
        'discount_amount' => 'integer',
        'cash_tendered' => 'integer',
        'change_amount' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
