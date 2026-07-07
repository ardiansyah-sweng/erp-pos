<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomerController;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/customers-test', [CustomerController::class, 'index']);
    }

    public function test_customer_index_returns_successful_response(): void
    {
        $response = $this->get('/customers-test');

        $response->assertStatus(200);
    }

    public function test_customer_index_returns_correct_view(): void
    {
        $response = $this->get('/customers-test');

        $response->assertStatus(200);
        $response->assertViewIs('customer.index');
    }

    public function test_customer_index_passes_customers_data_to_view(): void
    {
        Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@test.com'
        ]);

        $response = $this->get('/customers-test');

        $response->assertStatus(200);
        $response->assertViewHas('customers');

        $this->assertCount(
            1,
            $response->viewData('customers')
        );
    }

    public function test_customer_index_works_with_empty_database(): void
    {
        $response = $this->get('/customers-test');

        $response->assertStatus(200);
        $response->assertViewHas('customers');

        $this->assertCount(
            0,
            $response->viewData('customers')
        );
    }
}