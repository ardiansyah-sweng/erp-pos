<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class GetItemBySKUTest extends TestCase
{
  use RefreshDatabase;

  #[Test]
  public function dapat_menemukan_produk_berdasarkan_sku()
  {
    Product::create([
      'sku'           => 'PROD001',
      'name'          => 'Indomie Goreng',
      'selling_price' => 3500,
      'stock_quantity' => 100,
      'is_active'     => true,
    ]);

    $response = $this->getJson('/products/sku/PROD001');

    $response->assertStatus(200);
    $response->assertJson([
      'success' => true,
      'data' => [
        'sku'  => 'PROD001',
        'name' => 'Indomie Goreng',
      ],
    ]);
  }

  #[Test]
  public function mengembalikan_404_jika_sku_tidak_ditemukan()
  {
    $response = $this->getJson('/products/sku/TIDAKADA');

    $response->assertStatus(404);
    $response->assertJson([
      'success' => false,
    ]);
  }
}
