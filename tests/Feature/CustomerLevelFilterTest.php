<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerLevelFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_page_displays_level_filter(): void
    {
        $this->get(route('members.index'))
            ->assertOk()
            ->assertSee('filterMemberLevel', false)
            ->assertSee('Semua Level');
    }

    public function test_customer_list_can_be_filtered_by_member_level(): void
    {
        $this->createCustomer('CUS-001', 'Member Regular', '081111111111', 'Regular');
        $this->createCustomer('CUS-002', 'Member Gold', '082222222222', 'Gold');

        $response = $this->getJson('/customers?level=Gold');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customer_code', 'CUS-002')
            ->assertJsonPath('data.0.member_level', 'Gold');
    }

    public function test_member_level_filter_can_be_combined_with_search(): void
    {
        $this->createCustomer('CUS-001', 'Ayu Gold', '081111111111', 'Gold');
        $this->createCustomer('CUS-002', 'Budi Gold', '082222222222', 'Gold');
        $this->createCustomer('CUS-003', 'Ayu Silver', '083333333333', 'Silver');

        $response = $this->getJson('/customers?level=Gold&search=Ayu');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customer_code', 'CUS-001');
    }

    private function createCustomer(string $code, string $name, string $phone, string $level): Customer
    {
        return Customer::create([
            'customer_code' => $code,
            'name' => $name,
            'phone' => $phone,
            'member_level' => $level,
        ]);
    }
}
