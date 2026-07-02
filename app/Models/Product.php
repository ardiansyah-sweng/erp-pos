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

    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
    }

    public function scopeLowStock($query)
    {
        return $query
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock');
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'habis';
        }

        if ($this->stock_quantity <= $this->min_stock) {
            return 'menipis';
        }

        return 'aman';
    }
}
