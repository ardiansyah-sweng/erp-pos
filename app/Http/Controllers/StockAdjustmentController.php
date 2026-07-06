<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\StockAdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function index(Request $request, ProductService $productService): View
    {
        $search = trim((string) $request->query('search', ''));

        $products = $productService->searchPaginated($search, 15, (int) $request->query('page', 1), [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('stock-adjustments.index', compact('products', 'search'));
    }

    public function update(
        Request $request,
        Product $product,
        StockAdjustmentService $stockAdjustmentService,
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'movement_type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $stockAdjustmentService->adjustStock(
            $product,
            (int) $validated['quantity'],
            $validated['movement_type'],
            $validated['notes'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Stok berhasil diperbarui.',
            ]);
        }

        return back()->with(
            'success',
            "Stok {$product->name} berhasil diperbarui dari {$result['old_stock']} menjadi {$result['new_stock']}.",
        );
    }
}
