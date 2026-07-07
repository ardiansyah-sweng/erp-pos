<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CustomerService();
    }

    public function test_get_all_returns_customers(): void
    {
        Customer::create(['name' => 'Budi']);
        Customer::create(['name' => 'Andi']);

        $customers = $this->service->getAll();

        $this->assertCount(2, $customers);
    }

    public function test_find_by_id_returns_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Hilal'
        ]);

        $result = $this->service->findById($customer->id);

        $this->assertNotNull($result);
        $this->assertEquals('Hilal', $result->name);
    }

    public function test_create_customer(): void
    {
        $customer = $this->service->create([
            'name' => 'Customer Baru',
            'email' => 'baru@test.com'
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Customer Baru'
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function test_update_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Lama'
        ]);

        $updated = $this->service->update($customer->id, [
            'name' => 'Baru'
        ]);

        $this->assertEquals('Baru', $updated->fresh()->name);
    }

    public function test_delete_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Delete Me'
        ]);

        $result = $this->service->delete($customer->id);

        $this->assertTrue($result);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id
        ]);
    }

    public function test_search_customer(): void
    {
        Customer::create([
            'name' => 'Hilal',
            'email' => 'hilal@test.com',
            'is_active' => true,
        ]);

        $result = $this->service->search('Hil');

        $this->assertCount(1, $result);
    }
}