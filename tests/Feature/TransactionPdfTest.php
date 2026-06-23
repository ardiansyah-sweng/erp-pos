<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_transaction_pdf()
    {
        $product = Product::create([
            'sku' => 'SKU001',
            'barcode' => '123456',
            'name' => 'Produk PDF Test',
            'unit' => 'pcs',
            'selling_price' => 10000,
            'stock_quantity' => 100,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'total' => 10000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10000,
            'amount' => 10000,
        ]);

        $response = $this->get(
            '/transactions/' . $transaction->id . '/pdf'
        );

        $response->assertStatus(200);

        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('content-type')
        );

        $response->assertHeader(
            'content-type',
            'application/pdf'
        );
    }
}