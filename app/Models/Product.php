<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'selling_price' => 'integer',
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
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
