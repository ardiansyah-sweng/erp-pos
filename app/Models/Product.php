<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'barcode',
        'sku',
        'name',
        'description',
        'category_id',
        'unit',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'min_stock',
        'max_stock',
        'is_active',
        'image_url',
    ];

    /**
     * Ambil data produk berdasarkan SKU.
     *
     * @param  string  $sku
     * @return \App\Models\Product|null
     */
    public static function getItemBySKU(string $sku): ?self
    {
        return self::where('sku', $sku)->first();
    }
}