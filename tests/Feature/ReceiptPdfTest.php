<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_pdf_returns_200_and_pdf_content_type()
    {
        $storeResponse = $this->postJson('/transaction/store', [
            'total' => 25000,
            'details' => [
                ['product_id' => 'PRD001', 'quantity' => 2, 'price' => 12500],
            ],
        ]);

        $storeResponse->assertStatus(201);
        $transactionId = $storeResponse['data']['id'];

        $response = $this->get("/transactions/{$transactionId}/receipt");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_receipt_returns_404_for_invalid_id()
    {
        $this->get('/transactions/99999/receipt')->assertStatus(404);
    }
}
