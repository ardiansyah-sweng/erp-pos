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

    public function searchProduct($keyword)
    {
        $products = Product::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('sku', 'like', '%' . $keyword . '%')
            ->where('is_active', true)
            ->get();

        return $products;
    }
}