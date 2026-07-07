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

    /**
     * Mengembalikan daftar produk aktif dengan opsi sorting berdasarkan nama atau harga.
     *
     * Query params:
     *   - sort_by  : 'name' | 'selling_price'  (default: 'name')
     *   - sort_dir : 'asc'  | 'desc'           (default: 'asc')
     *   - search   : string opsional untuk filter nama/SKU/barcode
     *
     * Contoh: GET /products/sort?sort_by=selling_price&sort_dir=desc
     */
    public function sort(Request $request)
    {
        // Kolom yang diizinkan untuk sorting
        $allowedSortBy = ['name', 'selling_price'];

        $sortBy  = in_array($request->query('sort_by'), $allowedSortBy, true)
            ? $request->query('sort_by')
            : 'name';

        $sortDir = strtolower((string) $request->query('sort_dir', 'asc')) === 'desc'
            ? 'desc'
            : 'asc';

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
            ->orderBy($sortBy, $sortDir)
            ->get();

        return response()->json([
            'success'  => true,
            'sort_by'  => $sortBy,
            'sort_dir' => $sortDir,
            'total'    => $products->count(),
            'data'     => $products,
        ]);
    }

    public function getItemBySKU($sku)
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
