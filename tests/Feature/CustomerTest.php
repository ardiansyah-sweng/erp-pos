<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_customer_list()
    {
        Customer::factory()->count(3)->create();

        $response = $this->get('/customers');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_customer()
    {
        $response = $this->post('/customers', [
            'name' => 'Revi',
            'phone' => '08123456789',
            'email' => 'revi@gmail.com',
            'address' => 'Yogyakarta'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customers', [
            'phone' => '08123456789'
        ]);
    }

    /** @test */
    public function can_search_customer_by_phone()
    {
        Customer::factory()->create([
            'name' => 'Revi',
            'phone' => '08123456789'
        ]);

        $response = $this->get('/customers/search?phone=08123456789');

        $response->assertStatus(200);
    }
}