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
        'price',
        'cost',
        'stock',
        'sync_status',
    ];
}