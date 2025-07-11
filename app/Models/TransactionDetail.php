<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = 'transaction_detail';
    protected $fillable = ['transaction_id','product_id','quantity','price','amount', 'created_at', 'updated_at'];

    /**
     * Ambil nama kolom tertentu dari $fillable
     */
    public function getColumn($index)
    {
        $fillable = $this->getFillable();
        return $fillable[$index] ?? null;
    }

}
