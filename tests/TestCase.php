<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Sebagian besar route kini wajib login (lihat EnsureCashierAuthenticated).
        // Test yang ingin memeriksa perilaku belum login dapat memanggil
        // $this->flushSession() sebelum request.
        $this->withSession([
            'cashier_id' => 1,
            'cashier_name' => 'Test Cashier',
            'cashier_username' => 'test.cashier@erp.test',
            'cashier_role' => 'kasir',
        ]);
    }
}
