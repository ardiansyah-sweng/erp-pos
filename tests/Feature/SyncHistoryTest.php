<?php

namespace Tests\Feature;

use Tests\TestCase;

class SyncHistoryTest extends TestCase
{
    /**
     * Test halaman riwayat sinkronisasi dapat diakses.
     */
    public function test_sync_history_page_can_be_accessed(): void
    {
        $response = $this->get('/sync-histories');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Sinkronisasi');
    }
}