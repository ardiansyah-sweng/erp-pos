<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'product_id',
        'barcode',
        'name',
        'description',
        'category_id',
        'category_name',
        'unit',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'selling_price'  => 'integer',
        'stock_quantity' => 'integer',
        'min_stock'      => 'integer',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id', 'product_id');
    }

    public function getColumn(int $index): string
    {
        return $this->fillable[$index];
    }

    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
    }
}