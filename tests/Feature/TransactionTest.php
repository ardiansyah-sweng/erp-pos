<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function test_store_transaction(): void
    {
        $response = $this->postJson('/transaction/store', [
            'total' => 50000,
            'details' => [
                [
                    'product_id' => 'PRD001',
                    'quantity' => 2,
                    'price' => 10000,
                ],
            ],
        ]);

        $response->assertStatus(201);
    }
}