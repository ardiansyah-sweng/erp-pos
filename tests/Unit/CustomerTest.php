<?php

namespace Tests\Unit;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created(): void
    {
        $customer = Customer::create([
            'name' => 'Hilal',
            'email' => 'hilal@test.com',
            'phone' => '08123456789',
            'address' => 'Yogyakarta',
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Hilal',
            'email' => 'hilal@test.com',
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function test_active_scope_returns_only_active_customers(): void
    {
        Customer::create([
            'name' => 'Active Customer',
            'is_active' => true,
        ]);

        Customer::create([
            'name' => 'Inactive Customer',
            'is_active' => false,
        ]);

        $customers = Customer::active()->get();

        $this->assertCount(1, $customers);
        $this->assertEquals(
            'Active Customer',
            $customers->first()->name
        );
    }

    public function test_loyalty_points_cast_to_integer(): void
    {
        $customer = Customer::create([
            'name' => 'Point Customer',
            'loyalty_points' => '100',
        ]);

        $this->assertIsInt(
            $customer->fresh()->loyalty_points
        );
    }
}