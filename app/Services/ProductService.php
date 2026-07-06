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
        public function filterByPrice($price)
    {
        $query = Product::where('is_active', true);

        switch ($price) {

            case '1':
                $query->whereBetween('price', [0, 5000]);
                break;

            case '2':
                $query->whereBetween('price', [5001, 10000]);
                break;

            case '3':
                $query->whereBetween('price', [10001, 20000]);
                break;

            case '4':
                $query->where('price', '>', 20000);
                break;

            default:
                break;
        }

        return $query->orderBy('name')->get();
    }
}