<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransactionTest extends TestCase
{
    /**
     * Test store transaction
     */
    public function test_store_transaction()
    {
        $response = $this->postJson('/transaction/store', [
            'total' => 50000,
            'details' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 10000
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                    'price' => 30000
                ]
            ]
        ]);

        $response->assertStatus(201);
    }
}