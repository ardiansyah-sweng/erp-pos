<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getItemBySKU($sku)
    {
        $product = Product::where('sku', $sku)->first();

        return $product;
    }
}