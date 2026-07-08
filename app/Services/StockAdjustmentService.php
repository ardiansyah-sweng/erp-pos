<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
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

    public function importFromCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        $expected = ['sku', 'name', 'barcode', 'description', 'unit', 'selling_price', 'stock_quantity', 'min_stock'];
        $headerIndex = array_flip($headers);

        $missingColumns = array_diff($expected, $headers);
        if (!empty($missingColumns)) {
            fclose($handle);
            return [
                'success' => false,
                'message' => 'Format CSV tidak valid. Kolom yang diperlukan: ' . implode(', ', $expected) . '. Kolom yang ditemukan: ' . implode(', ', $headers),
                'created' => 0,
                'skipped' => 0,
                'errors' => ['Kolom tidak lengkap: ' . implode(', ', $missingColumns)],
            ];
        }

        $created = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $row = array_map('trim', $row);

            if (count($row) < count($expected)) {
                $row = array_pad($row, count($expected), '');
            }

            $data = array_combine($headers, $row);

            $sku = $data['sku'] ?? '';
            $name = $data['name'] ?? '';

            if (empty($sku) || empty($name)) {
                $skipped++;
                $errors[] = "Baris {$rowNumber}: SKU dan Nama wajib diisi, dilewati.";
                continue;
            }

            if (Product::where('sku', $sku)->exists()) {
                $skipped++;
                $errors[] = "Baris {$rowNumber}: SKU '{$sku}' sudah terdaftar, dilewati.";
                continue;
            }

            $barcode = $data['barcode'] ?? '';
            if (!empty($barcode) && Product::where('barcode', $barcode)->exists()) {
                $skipped++;
                $errors[] = "Baris {$rowNumber}: Barcode '{$barcode}' sudah terdaftar, dilewati.";
                continue;
            }

            Product::create([
                'sku' => $sku,
                'name' => $name,
                'barcode' => $barcode ?: null,
                'description' => $data['description'] ?? null,
                'unit' => $data['unit'] ?: 'pcs',
                'selling_price' => (int) ($data['selling_price'] ?? 0),
                'stock_quantity' => max(0, (int) ($data['stock_quantity'] ?? 0)),
                'min_stock' => max(0, (int) ($data['min_stock'] ?? 0)),
                'is_active' => true,
            ]);

            $created++;
        }

        fclose($handle);

        $message = "Import selesai: {$created} produk berhasil ditambahkan.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return [
            'success' => $created > 0,
            'message' => $message,
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $skipped > 0 ? $errors : [],
        ];
    }
}
