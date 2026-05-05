<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'id';
    public $incrementing = false;       // karena id bukan auto-increment
    protected $keyType = 'string';      // karena id bertipe char/string

    protected $fillable = [
        'id',
        'name',
        'category',
        'price',
        'stock',
        'description',
    ];

    // Relasi ke TransactionDetail
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id', 'id');
    }

    // Helper getColumn() seperti yang dipakai di TransactionSeeder
    public function getColumn(int $index): string
    {
        return $this->fillable[$index];
    }
}