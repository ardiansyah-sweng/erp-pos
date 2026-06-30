<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionExportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dapat_mengexport_csv_tanpa_filter_tanggal()
    {
        // 1. Setup Product
        $product = Product::create([
            'barcode' => '1234567890123',
            'sku' => 'PROD-001',
            'name' => 'Produk Keren',
            'selling_price' => 15000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // 2. Setup Transaction
        $transaction = Transaction::create([
            'total' => 30000,
        ]);
        // Set created_at explicitly (force updated because it's set on save)
        $transaction->created_at = now()->setDate(2026, 6, 30)->setTime(10, 0, 0);
        $transaction->save();

        // 3. Setup Transaction Details
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => (string) $product->id,
            'quantity' => 2,
            'price' => 15000,
            'amount' => 30000,
        ]);

        // 4. Setup Payment
        $transaction->payments()->create([
            'payment_method' => 'card',
            'amount' => 30000,
            'payment_status' => 'success',
            'discount_amount' => 0,
            'cash_tendered' => 30000,
            'change_amount' => 0,
        ]);

        // 5. Send Export Request
        $response = $this->get(route('transactions.export'));

        // 6. Assertions
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=detail-transaksi.csv');

        // Capture streamed content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Clean BOM from the response content if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        // Parse CSV content
        $rows = array_map('str_getcsv', explode("\n", rtrim($content)));

        $this->assertEquals('Kode Transaksi', $rows[0][0]);
        $this->assertEquals('Tanggal', $rows[0][1]);
        $this->assertEquals('Metode Pembayaran', $rows[0][3]);

        $this->assertCount(2, $rows); // Header + 1 Detail row
        $detailRow = $rows[1];

        $expectedTrxCode = 'TRX-' . str_pad((string)$transaction->id, 4, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedTrxCode, $detailRow[0]);
        $this->assertEquals('30/06/2026', $detailRow[1]);
        $this->assertEquals('10:00', $detailRow[2]);
        $this->assertEquals('card', $detailRow[3]);
        $this->assertEquals('PROD-001', $detailRow[4]);
        $this->assertEquals('Produk Keren', $detailRow[5]);
        $this->assertEquals('2', $detailRow[6]);
        $this->assertEquals('15000', $detailRow[7]);
        $this->assertEquals('30000', $detailRow[8]);
        $this->assertEquals('0', $detailRow[9]);
        $this->assertEquals('30000', $detailRow[10]);
        $this->assertEquals('30000', $detailRow[11]);
        $this->assertEquals('0', $detailRow[12]);
    }

    #[Test]
    public function dapat_mengexport_csv_dengan_filter_tanggal()
    {
        // 1. Setup Products
        $product = Product::create([
            'barcode' => '1234567890123',
            'sku' => 'PROD-001',
            'name' => 'Produk A',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // 2. Transaction on 2026-06-30
        $trxTarget = Transaction::create([
            'total' => 10000,
        ]);
        $trxTarget->created_at = '2026-06-30 10:00:00';
        $trxTarget->save();

        TransactionDetail::create([
            'transaction_id' => $trxTarget->id,
            'product_id' => (string) $product->id,
            'quantity' => 1,
            'price' => 10000,
            'amount' => 10000,
        ]);
        $trxTarget->payments()->create([
            'payment_method' => 'cash',
            'amount' => 10000,
            'payment_status' => 'success',
            'discount_amount' => 0,
            'cash_tendered' => 10000,
            'change_amount' => 0,
        ]);

        // 3. Transaction on 2026-07-01 (should be filtered out)
        $trxOther = Transaction::create([
            'total' => 10000,
        ]);
        $trxOther->created_at = '2026-07-01 12:00:00';
        $trxOther->save();

        TransactionDetail::create([
            'transaction_id' => $trxOther->id,
            'product_id' => (string) $product->id,
            'quantity' => 1,
            'price' => 10000,
            'amount' => 10000,
        ]);
        $trxOther->payments()->create([
            'payment_method' => 'cash',
            'amount' => 10000,
            'payment_status' => 'success',
            'discount_amount' => 0,
            'cash_tendered' => 10000,
            'change_amount' => 0,
        ]);

        // 4. Send Export Request for 2026-06-30
        $response = $this->get(route('transactions.export', ['date' => '2026-06-30']));

        // 5. Assertions
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=detail-transaksi-2026-06-30.csv');

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Clean BOM from the response content if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $rows = array_map('str_getcsv', explode("\n", rtrim($content)));
        
        $this->assertCount(2, $rows); // Header + 1 target transaction detail row

        $expectedTrxCode = 'TRX-' . str_pad((string)$trxTarget->id, 4, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedTrxCode, $rows[1][0]);
        $this->assertEquals('30/06/2026', $rows[1][1]);
    }

    #[Test]
    public function mengembalikan_csv_kosong_jika_tidak_ada_transaksi()
    {
        $response = $this->get(route('transactions.export'));

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Clean BOM from the response content if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $rows = array_map('str_getcsv', explode("\n", rtrim($content)));
        $this->assertCount(1, $rows); // Only headers
    }

    #[Test]
    public function dapat_mengalokasikan_diskon_secara_proporsional()
    {
        // Setup Product
        $productA = Product::create([
            'barcode' => '1234567890123',
            'sku' => 'PROD-A',
            'name' => 'Produk A',
            'selling_price' => 10000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $productB = Product::create([
            'barcode' => '9876543210987',
            'sku' => 'PROD-B',
            'name' => 'Produk B',
            'selling_price' => 20000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'total' => 25000,
        ]);
        $transaction->created_at = '2026-06-30 10:00:00';
        $transaction->save();

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => (string) $productA->id,
            'quantity' => 1,
            'price' => 10000,
            'amount' => 10000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => (string) $productB->id,
            'quantity' => 1,
            'price' => 20000,
            'amount' => 20000,
        ]);

        $transaction->payments()->create([
            'payment_method' => 'cash',
            'amount' => 25000,
            'payment_status' => 'success',
            'discount_amount' => 5000,
            'cash_tendered' => 30000,
            'change_amount' => 5000,
        ]);

        $response = $this->get(route('transactions.export'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Clean BOM from the response content if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $rows = array_map('str_getcsv', explode("\n", rtrim($content)));
        $this->assertCount(3, $rows); // Header + 2 details

        // Item A detail row: discount is 1666
        $this->assertEquals('1666', $rows[1][9]); // Diskon Item
        $this->assertEquals('8334', $rows[1][10]); // Total Setelah Diskon

        // Item B detail row: discount is 3334
        $this->assertEquals('3334', $rows[2][9]); // Diskon Item
        $this->assertEquals('16666', $rows[2][10]); // Total Setelah Diskon
    }

    #[Test]
    public function menggunakan_default_untuk_produk_yang_dihapus_atau_hilang()
    {
        $transaction = Transaction::create([
            'total' => 10000,
        ]);
        $transaction->created_at = '2026-06-30 10:00:00';
        $transaction->save();

        // Product ID that does not exist in products table
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => '99999',
            'quantity' => 1,
            'price' => 10000,
            'amount' => 10000,
        ]);

        $transaction->payments()->create([
            'payment_method' => 'cash',
            'amount' => 10000,
            'payment_status' => 'success',
            'discount_amount' => 0,
            'cash_tendered' => 10000,
            'change_amount' => 0,
        ]);

        $response = $this->get(route('transactions.export'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Clean BOM from the response content if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $rows = array_map('str_getcsv', explode("\n", rtrim($content)));
        
        $this->assertEquals('-', $rows[1][4]); // SKU
        $this->assertEquals('Produk #99999', $rows[1][5]); // Name
    }
}
