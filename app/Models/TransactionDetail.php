<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class TransactionDetail extends Model
{
    protected $table = 'transaction_detail';
    protected $fillable = ['transaction_id','product_id','quantity','price','amount', 'created_at', 'updated_at'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

<<<<<<< HEAD
/**
 * Ambil nama kolom tertentu dari $fillable
 */
=======
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Ambil nama kolom tertentu dari $fillable
     */
>>>>>>> develop
    public function getColumn($index)
    {
        $fillable = $this->getFillable();
        return $fillable[$index] ?? null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
