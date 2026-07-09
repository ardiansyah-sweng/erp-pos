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
        'parking_fee',
        'parking_type',
    ];

    protected $casts = [
        'amount' => 'integer',
        'discount_amount' => 'integer',
        'cash_tendered' => 'integer',
        'change_amount' => 'integer',
        'parking_fee' => 'integer',
        'parking_type' => 'string',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
