<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
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

        $products = Product::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

<<<<<<< HEAD
    function searchProduct(Request $request)
    {
        $keyword = $request->query('q', '');

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('sku', 'like', '%' . $keyword . '%');
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $products,
=======
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
>>>>>>> 86fb96b7ab46dc498fd8d09d9f92d7bc066ddea3
        ], 200);
    }
}