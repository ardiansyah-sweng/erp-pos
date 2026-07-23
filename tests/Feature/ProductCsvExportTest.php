<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_downloads_products_from_the_table_as_csv(): void
    {
        $category = Category::create([
            'name' => 'Minuman',
            'is_active' => true,
        ]);
        Product::create([
            'sku' => 'PRD-200',
            'name' => 'Teh Lokal',
            'barcode' => '8992000000001',
            'category_id' => $category->id,
            'unit' => 'botol',
            'selling_price' => 7000,
            'stock_quantity' => 25,
            'min_stock' => 5,
            'description' => 'Teh produksi lokal',
            'is_active' => true,
        ]);

        $response = $this->get(route('products.export'));

        $response->assertOk()
            ->assertDownload('produk-'.now()->format('Y-m-d').'.csv');

        $rows = $this->csvRows($response->streamedContent());

        $this->assertSame([
            'sku',
            'name',
            'barcode',
            'category',
            'unit',
            'selling_price',
            'stock_quantity',
            'min_stock',
            'description',
            'is_active',
        ], $rows[0]);
        $this->assertSame([
            'PRD-200',
            'Teh Lokal',
            '8992000000001',
            'Minuman',
            'botol',
            '7000',
            '25',
            '5',
            'Teh produksi lokal',
            '1',
        ], $rows[1]);
    }

    public function test_export_respects_the_product_search_filter(): void
    {
        Product::create([
            'sku' => 'PRD-AQUA',
            'name' => 'Aqua 600ml',
            'selling_price' => 4000,
            'stock_quantity' => 20,
            'min_stock' => 5,
        ]);
        Product::create([
            'sku' => 'PRD-ROTI',
            'name' => 'Roti Tawar',
            'selling_price' => 15000,
            'stock_quantity' => 10,
            'min_stock' => 2,
        ]);

        $response = $this->get(route('products.export', ['search' => 'Aqua']));
        $rows = $this->csvRows($response->streamedContent());

        $this->assertCount(2, $rows);
        $this->assertSame('PRD-AQUA', $rows[1][0]);
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function csvRows(string $content): array
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        return array_map(
            fn (string $line) => str_getcsv($line, ',', '"', ''),
            preg_split('/\r\n|\r|\n/', trim($content))
        );
    }
}
