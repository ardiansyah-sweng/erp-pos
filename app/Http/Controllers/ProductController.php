<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Ambil semua produk dari server eksternal.
     */
    public function getProducts()
    {
        $products = Http::get('http://127.0.0.1:8000/products')->json();

        return response()->json($products);
    }

    /**
     * Ambil satu produk berdasarkan SKU.
     *
     * @param  string  $sku
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemBySKU(string $sku): JsonResponse
    {
        $product = Product::getItemBySKU($sku);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk dengan SKU "' . $sku . '" tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product,
        ], 200);
    }
}