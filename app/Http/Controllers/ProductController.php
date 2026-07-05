<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProducts(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        return response()->json([
            'success' => true,
            'data' => $this->productService->search($search),
        ]);
    }

    function getItemBySKU($sku)
    {
        $product = $this->productService->getItemBySKU($sku);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk dengan SKU ' . $sku . ' tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditemukan.',
            'data'    => $product,
        ], 200);
    }
}