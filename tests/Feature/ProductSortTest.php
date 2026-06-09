<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSortTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat beberapa produk dummy untuk keperluan testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Product::insert([
            [
                'sku'            => 'PRD-T01',
                'name'           => 'Zebra Snack',
                'selling_price'  => 15000,
                'stock_quantity' => 10,
                'min_stock'      => 2,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'sku'            => 'PRD-T02',
                'name'           => 'Aqua Botol',
                'selling_price'  => 4000,
                'stock_quantity' => 50,
                'min_stock'      => 5,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'sku'            => 'PRD-T03',
                'name'           => 'Mie Goreng',
                'selling_price'  => 3500,
                'stock_quantity' => 30,
                'min_stock'      => 5,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'sku'            => 'PRD-T04',
                'name'           => 'Produk Nonaktif',
                'selling_price'  => 5000,
                'stock_quantity' => 10,
                'min_stock'      => 2,
                'is_active'      => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }

    /**
     * Test 1: Response struktur lengkap
     * Endpoint harus mengembalikan key success, sort_by, sort_dir, total, data.
     */
    public function test_response_memiliki_struktur_yang_benar(): void
    {
        $response = $this->getJson('/products/sort');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'sort_by',
                     'sort_dir',
                     'total',
                     'data' => [
                         '*' => ['id', 'sku', 'name', 'selling_price'],
                     ],
                 ])
                 ->assertJson(['success' => true]);
    }

    /**
     * Test 2: Sort default (tanpa parameter)
     * Harus urut nama A-Z dan hanya produk aktif.
     */
    public function test_sort_default_urut_nama_ascending(): void
    {
        $response = $this->getJson('/products/sort');

        $response->assertStatus(200)
                 ->assertJson([
                     'sort_by'  => 'name',
                     'sort_dir' => 'asc',
                     'total'    => 3,
                 ]);

        $names = collect($response->json('data'))->pluck('name')->values()->toArray();
        $this->assertEquals(['Aqua Botol', 'Mie Goreng', 'Zebra Snack'], $names);
    }

    /**
     * Test 3: Sort nama Z-A
     */
    public function test_sort_nama_descending(): void
    {
        $response = $this->getJson('/products/sort?sort_by=name&sort_dir=desc');

        $response->assertStatus(200)
                 ->assertJson([
                     'sort_by'  => 'name',
                     'sort_dir' => 'desc',
                 ]);

        $names = collect($response->json('data'))->pluck('name')->values()->toArray();
        $this->assertEquals(['Zebra Snack', 'Mie Goreng', 'Aqua Botol'], $names);
    }

    /**
     * Test 4: Sort harga termurah dulu (ascending)
     */
    public function test_sort_harga_ascending(): void
    {
        $response = $this->getJson('/products/sort?sort_by=selling_price&sort_dir=asc');

        $response->assertStatus(200)
                 ->assertJson([
                     'sort_by'  => 'selling_price',
                     'sort_dir' => 'asc',
                 ]);

        $prices = collect($response->json('data'))->pluck('selling_price')->values()->toArray();
        $this->assertEquals([3500, 4000, 15000], $prices);
    }

    /**
     * Test 5: Sort harga termahal dulu (descending)
     */
    public function test_sort_harga_descending(): void
    {
        $response = $this->getJson('/products/sort?sort_by=selling_price&sort_dir=desc');

        $response->assertStatus(200)
                 ->assertJson([
                     'sort_by'  => 'selling_price',
                     'sort_dir' => 'desc',
                 ]);

        $prices = collect($response->json('data'))->pluck('selling_price')->values()->toArray();
        $this->assertEquals([15000, 4000, 3500], $prices);
    }

    /**
     * Test 6: Kombinasi sort + search
     * Cari "a" lalu sort harga ascending — hasilnya hanya produk aktif yang cocok.
     */
    public function test_sort_dengan_search(): void
    {
        $response = $this->getJson('/products/sort?sort_by=selling_price&sort_dir=asc&search=a');

        $response->assertStatus(200);

        $data = collect($response->json('data'));

        // Semua hasil harus mengandung huruf 'a' di nama/sku
        $data->each(function ($product) {
            $this->assertTrue(
                str_contains(strtolower($product['name']), 'a') ||
                str_contains(strtolower($product['sku']), 'a'),
                "Produk '{$product['name']}' tidak cocok dengan keyword 'a'"
            );
        });

        // Urutan harga harus ascending
        $prices = $data->pluck('selling_price')->values()->toArray();
        $sorted = $prices;
        sort($sorted);
        $this->assertEquals($sorted, $prices);
    }

    /**
     * Test 7: sort_by dengan nilai tidak valid
     * Harus fallback ke sort nama ascending.
     */
    public function test_sort_by_tidak_valid_fallback_ke_nama(): void
    {
        $response = $this->getJson('/products/sort?sort_by=invalid_column&sort_dir=asc');

        $response->assertStatus(200)
                 ->assertJson([
                     'sort_by'  => 'name',
                     'sort_dir' => 'asc',
                 ]);
    }

    /**
     * Test 8: sort_dir dengan nilai tidak valid
     * Harus fallback ke asc.
     */
    public function test_sort_dir_tidak_valid_fallback_ke_asc(): void
    {
        $response = $this->getJson('/products/sort?sort_by=name&sort_dir=random');

        $response->assertStatus(200)
                 ->assertJson(['sort_dir' => 'asc']);
    }

    /**
     * Test 9: Produk nonaktif tidak ikut muncul
     */
    public function test_produk_nonaktif_tidak_muncul(): void
    {
        $response = $this->getJson('/products/sort');

        $response->assertStatus(200);

        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertNotContains('Produk Nonaktif', $names);
    }

    /**
     * Test 10: Total sesuai jumlah produk aktif
     */
    public function test_total_sesuai_jumlah_produk_aktif(): void
    {
        $response = $this->getJson('/products/sort');

        $response->assertStatus(200)
                 ->assertJson(['total' => 3]);
    }
}
