<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransactionDetail extends Model
{
    protected $fillable = [
        'return_transaction_id',
        'transaction_detail_id',
        'product_id',
        'quantity',
        'price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'amount' => 'integer',
    ];

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class, 'return_transaction_id');
    }

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class, 'transaction_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
