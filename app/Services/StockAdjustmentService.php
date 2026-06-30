<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function adjustStock(
        Product $product,
        int $quantity,
        string $movementType,
        ?string $notes = null,
    ): array {
        return DB::transaction(function () use ($product, $quantity, $movementType, $notes) {
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            $oldStock = (int) $lockedProduct->stock_quantity;
            $stockChange = match ($movementType) {
                'in' => abs($quantity),
                'out' => -abs($quantity),
                'adjustment' => $quantity,
            };
            $newStock = $oldStock + $stockChange;

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak mencukupi. Stok saat ini {$oldStock}.",
                ]);
            }

            $lockedProduct->update(['stock_quantity' => $newStock]);

            return [
                'product_id' => $lockedProduct->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'movement' => [
                    'movement_type' => $movementType,
                    'quantity' => $stockChange,
                    'notes' => $notes,
                    'created_at' => now()->toISOString(),
                ],
            ];
        });
    }
}
