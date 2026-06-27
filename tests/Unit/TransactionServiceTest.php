<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_transaction_and_details(): void
    {
        $productA = Product::create([
            'barcode' => '1234567890123',
            'sku' => 'PRD-A001',
            'name' => 'Produk A',
            'description' => 'Produk A sample',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'min_stock' => 1,
            'is_active' => true,
        ]);

        $productB = Product::create([
            'barcode' => '9876543210987',
            'sku' => 'PRD-B001',
            'name' => 'Produk B',
            'description' => 'Produk B sample',
            'unit' => 'pcs',
            'selling_price' => 20000,
            'stock_quantity' => 5,
            'min_stock' => 1,
            'is_active' => true,
        ]);

        $payload = [
            'items' => [
                [
                    'product_id' => $productA->id,
                    'quantity' => 2,
                    'unit_price' => 10000,
                ],
                [
                    'product_id' => $productB->id,
                    'quantity' => 1,
                    'unit_price' => 20000,
                ],
            ],
            'discount_amount' => 5000,
            'payment_method' => 'cash',
            'cash_tendered' => 50000,
            'notes' => 'Pembayaran tunai',
        ];

        $service = new TransactionService();
        $transaction = $service->store($payload);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertDatabaseHas('transaction', [
            'id' => $transaction->id,
            'total' => 35000,
        ]);

        $this->assertDatabaseHas('transaction_detail', [
            'transaction_id' => $transaction->id,
            'product_id' => (string) $productA->id,
            'quantity' => 2,
            'price' => 10000,
            'amount' => 20000,
        ]);

        $this->assertDatabaseHas('transaction_detail', [
            'transaction_id' => $transaction->id,
            'product_id' => (string) $productB->id,
            'quantity' => 1,
            'price' => 20000,
            'amount' => 20000,
        ]);

        $this->assertCount(2, $transaction->details);
    }
}
