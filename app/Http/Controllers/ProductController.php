<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(): JsonResponse
    {
        $search = trim((string) request()->query('search', ''));

        $products = Product::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%')
                      ->orWhere('barcode', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'product_id' => $p->sku,
                'name'       => $p->name,
                'barcode'    => $p->barcode,
            ]);

        return response()->json($products);
    }

    public function getPrices(): JsonResponse
    {
        $products = Product::all();

        $prices = $products->flatMap(function ($product, $index) {
            return [
                [
                    'id'                 => ($index * 2) + 1,
                    'product_id'         => $product->sku,
                    'now_avg_base_price' => (int) ($product->selling_price * 0.8),
                    'created_at'         => now()->subDays(14)->format('Y-m-d H:i:s'),
                ],
                [
                    'id'                 => ($index * 2) + 2,
                    'product_id'         => $product->sku,
                    'now_avg_base_price' => $product->selling_price,
                    'created_at'         => now()->subDays(7)->format('Y-m-d H:i:s'),
                ],
            ];
        })->values();

        return response()->json($prices);
    }

    public function getApiProducts(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get()->map(fn ($p) => [
            'id'             => $p->id,
            'barcode'        => $p->barcode,
            'sku'            => $p->sku,
            'name'           => $p->name,
            'description'    => $p->description,
            'unit'           => $p->unit,
            'selling_price'  => $p->selling_price,
            'stock_quantity' => $p->stock_quantity,
            'min_stock'      => $p->min_stock,
            'is_active'      => $p->is_active,
            'image_url'      => null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $products->values()->all(),
            'meta'    => [
                'current_page' => 1,
                'last_page'    => 1,
                'per_page'     => $products->count(),
                'total'        => $products->count(),
                'from'         => $products->isEmpty() ? null : 1,
                'to'           => $products->isEmpty() ? null : $products->count(),
            ],
        ]);
    }
}