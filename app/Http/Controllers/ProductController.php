<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(): JsonResponse
    {
        return response()->json($this->legacyProducts());
    }

    public function getPrices(): JsonResponse
    {
        return response()->json($this->priceCatalog());
    }

    public function getApiProducts(Request $request): JsonResponse
    {
        $products = collect($this->apiProducts());

        if ($search = $request->string('search')->trim()->value()) {
            $products = $products->filter(function (array $product) use ($search) {
                return str_contains(strtolower($product['name']), strtolower($search))
                    || str_contains(strtolower($product['sku']), strtolower($search))
                    || str_contains(strtolower($product['barcode']), strtolower($search));
            })->values();
        }

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

    private function legacyProducts(): array
    {
        return collect($this->baseProducts())
            ->map(fn (array $product) => [
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'barcode'    => $product['barcode'],
            ])
            ->values()
            ->all();
    }

    private function priceCatalog(): array
    {
        return collect($this->baseProducts())
            ->flatMap(function (array $product, int $index) {
                return [
                    [
                        'id'                 => ($index * 2) + 1,
                        'product_id'         => $product['product_id'],
                        'now_avg_base_price' => $product['cost_price'],
                        'created_at'         => now()->subDays(14)->format('Y-m-d H:i:s'),
                    ],
                    [
                        'id'                 => ($index * 2) + 2,
                        'product_id'         => $product['product_id'],
                        'now_avg_base_price' => $product['selling_price'],
                        'created_at'         => now()->subDays(7)->format('Y-m-d H:i:s'),
                    ],
                ];
            })
            ->values()
            ->all();
    }

    private function apiProducts(): array
    {
        return collect($this->baseProducts())
            ->map(fn (array $product) => [
                'id'             => $product['id'],
                'barcode'        => $product['barcode'],
                'sku'            => $product['product_id'],
                'name'           => $product['name'],
                'description'    => $product['description'],
                'category'       => [
                    'id'   => $product['category_id'],
                    'name' => $product['category_name'],
                ],
                'unit'           => $product['unit'],
                'cost_price'     => $product['cost_price'],
                'selling_price'  => $product['selling_price'],
                'stock_quantity' => $product['stock_quantity'],
                'min_stock'      => $product['min_stock'],
                'is_active'      => true,
                'image_url'      => null,
            ])
            ->values()
            ->all();
    }

    private function baseProducts(): array
    {
        return [
            [
                'id'             => 1,
                'product_id'     => 'PRD001',
                'barcode'        => '899100000001',
                'name'           => 'Beras Premium 5kg',
                'description'    => 'Beras premium untuk kebutuhan harian.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'pack',
                'cost_price'     => 68000,
                'selling_price'  => 72000,
                'stock_quantity' => 40,
                'min_stock'      => 5,
            ],
            [
                'id'             => 2,
                'product_id'     => 'PRD002',
                'barcode'        => '899100000002',
                'name'           => 'Minyak Goreng 1L',
                'description'    => 'Minyak goreng kemasan 1 liter.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'botol',
                'cost_price'     => 17000,
                'selling_price'  => 19500,
                'stock_quantity' => 75,
                'min_stock'      => 10,
            ],
            [
                'id'             => 3,
                'product_id'     => 'PRD003',
                'barcode'        => '899100000003',
                'name'           => 'Gula Pasir 1kg',
                'description'    => 'Gula pasir kristal putih.',
                'category_id'    => 1,
                'category_name'  => 'Sembako',
                'unit'           => 'pack',
                'cost_price'     => 14500,
                'selling_price'  => 16000,
                'stock_quantity' => 60,
                'min_stock'      => 8,
            ],
            [
                'id'             => 4,
                'product_id'     => 'PRD004',
                'barcode'        => '899100000004',
                'name'           => 'Mi Instan Goreng',
                'description'    => 'Mi instan rasa goreng.',
                'category_id'    => 2,
                'category_name'  => 'Makanan Cepat Saji',
                'unit'           => 'pcs',
                'cost_price'     => 2800,
                'selling_price'  => 3500,
                'stock_quantity' => 200,
                'min_stock'      => 25,
            ],
            [
                'id'             => 5,
                'product_id'     => 'PRD005',
                'barcode'        => '899100000005',
                'name'           => 'Air Mineral 600ml',
                'description'    => 'Air mineral botol 600ml.',
                'category_id'    => 3,
                'category_name'  => 'Minuman',
                'unit'           => 'botol',
                'cost_price'     => 2500,
                'selling_price'  => 4000,
                'stock_quantity' => 120,
                'min_stock'      => 20,
            ],
        ];
    }
}