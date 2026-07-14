<?php
namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    // ============================================
    // TEST METHOD: UPDATE (3 Test Case)
    // ============================================

    /**
     * Test Update Berhasil
     */
    public function test_update_customer_returns_success()
    {
        // ARRANGE
        $customer = Customer::factory()->create([
            'name' => 'Jasman Lama',
            'phone' => '081234567890'
        ]);

        // ACT
        $response = $this->putJson(
            "/customers/{$customer->id}",
            [
                'name' => 'Jasman Baru',
                'phone' => '082345678901',
                'email' => 'jasman@gmail.com',
                'address' => 'Jl. Merdeka No. 123'
            ],
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Customer berhasil diperbarui'
        ]);
        
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Jasman Baru'
        ]);
    }

    /**
     * Test Update Gagal: Customer tidak ditemukan
     */
    public function test_update_customer_not_found()
    {
        // ACT
        $response = $this->putJson(
            "/customers/999",
            ['name' => 'Jasman Baru'],
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Customer tidak ditemukan'
        ]);
    }

    /**
     * Test Update Validasi: Nama kosong
     */
    public function test_update_customer_validation_fails()
    {
        // ARRANGE
        $customer = Customer::factory()->create();

        // ACT
        $response = $this->putJson(
            "/customers/{$customer->id}",
            ['name' => ''], // KOSONG
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    // ============================================
    // TEST METHOD: DESTROY (3 Test Case)
    // ============================================

    /**
     * Test Delete Berhasil
     */
    public function test_destroy_customer_returns_success()
    {
        // ARRANGE
        $customer = Customer::factory()->create(['name' => 'Jasman']);
        $customerId = $customer->id;

        // ACT
        $response = $this->deleteJson(
            "/customers/{$customerId}",
            [],
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
        
        $this->assertDatabaseMissing('customers', ['id' => $customerId]);
    }

    /**
     * Test Delete Gagal: Customer tidak ditemukan
     */
    public function test_destroy_customer_not_found()
    {
        // ACT
        $response = $this->deleteJson(
            "/customers/999",
            [],
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Customer tidak ditemukan'
        ]);
    }

    /**
     * Test Delete Hanya Customer Spesifik
     */
    public function test_destroy_only_specified_customer()
    {
        // ARRANGE
        $customer1 = Customer::factory()->create(['name' => 'Jasman']);
        $customer2 = Customer::factory()->create(['name' => 'Budi']);

        // ACT
        $response = $this->deleteJson(
            "/customers/{$customer1->id}",
            [],
            ['X-CSRF-TOKEN' => csrf_token()]
        );

        // ASSERT
        $response->assertStatus(200);
        $this->assertDatabaseMissing('customers', ['id' => $customer1->id]);
        $this->assertDatabaseHas('customers', ['id' => $customer2->id]);
    }
}