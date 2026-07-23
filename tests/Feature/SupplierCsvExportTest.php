<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCsvExportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_supplier_export_downloads_utf8_csv(): void
    {
        Carbon::setTestNow('2026-07-23 12:00:00');

        Supplier::create([
            'name' => 'PT Maju, Bersama',
            'contact_person' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@maju.test',
            'address' => 'Jl. Merdeka No. 1, Bandung',
            'is_active' => true,
        ]);

        Supplier::create([
            'name' => 'CV Tidak Aktif',
            'is_active' => false,
        ]);

        $response = $this->get(route('suppliers.export'));

        $response->assertOk()
            ->assertDownload('supplier-2026-07-23.csv')
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $rows = $this->csvRows($response->streamedContent());

        $this->assertSame([
            'Nama Supplier',
            'Kontak Person',
            'Telepon',
            'Email',
            'Alamat',
            'Status',
        ], $rows[0]);
        $this->assertSame('CV Tidak Aktif', $rows[1][0]);
        $this->assertSame('Nonaktif', $rows[1][5]);
        $this->assertSame('PT Maju, Bersama', $rows[2][0]);
        $this->assertSame('Jl. Merdeka No. 1, Bandung', $rows[2][4]);
        $this->assertSame('Aktif', $rows[2][5]);
    }

    public function test_empty_supplier_export_contains_only_the_header(): void
    {
        $response = $this->get(route('suppliers.export'));

        $response->assertOk();

        $rows = $this->csvRows($response->streamedContent());

        $this->assertCount(1, $rows);
    }

    private function csvRows(string $content): array
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $rows = [];

        while (($row = fgetcsv($stream)) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }
}
