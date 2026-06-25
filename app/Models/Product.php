<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'sku',
        'name',
        'description',
        'unit',
        'selling_price',
        'stock_quantity',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'selling_price' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id', 'sku');
    }

    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
    }
}
