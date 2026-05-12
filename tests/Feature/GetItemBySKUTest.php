<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetItemBySKUTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function dapat_menemukan_produk_berdasarkan_sku()
  {
    // Siapkan data produk dummy di database
    Product::create([
      'sku'        => 'PROD001',
      'name'       => 'Indomie Goreng',
      'price'      => 3500,
      'cost'       => 2000,
      'stock'      => 100,
      'is_active'  => true,
      'sync_status' => 'synced',
    ]);

    // Kirim request ke endpoint
    $response = $this->get('/products/sku/PROD001');

    // Cek hasilnya
    $response->assertStatus(200);
    $response->assertJson([
      'success' => true,
      'data' => [
        'sku'  => 'PROD001',
        'name' => 'Indomie Goreng',
      ],
    ]);
  }

  /** @test */
  public function mengembalikan_404_jika_sku_tidak_ditemukan()
  {
    // Kirim request dengan SKU yang tidak ada
    $response = $this->get('/products/sku/TIDAKADA');

    // Cek hasilnya
    $response->assertStatus(404);
    $response->assertJson([
      'success' => false,
    ]);
  }
}
