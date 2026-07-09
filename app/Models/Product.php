<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'sku',
        'name',
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
    }
}