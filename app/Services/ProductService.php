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
<<<<<<< HEAD

    public function searchProduct($keyword)
    {
        $products = Product::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('sku', 'like', '%' . $keyword . '%')
            ->where('is_active', true)
            ->get();

        return $products;
    }
=======
>>>>>>> 86fb96b7ab46dc498fd8d09d9f92d7bc066ddea3
}