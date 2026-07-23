<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_page_provides_detail_action_and_modal(): void
    {
        $supplier = Supplier::create([
            'name' => 'PT Detail Supplier',
            'contact_person' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@detail.test',
            'address' => 'Jl. Contoh No. 1',
            'is_active' => true,
        ]);

        $response = $this->get(route('suppliers.index'));

        $response->assertOk()
            ->assertSee('id="btn-detail-'.$supplier->id.'"', false)
            ->assertSee('data-name="PT Detail Supplier"', false)
            ->assertSee('data-contact="Budi Santoso"', false)
            ->assertSee('data-phone="081234567890"', false)
            ->assertSee('data-email="budi@detail.test"', false)
            ->assertSee('data-address="Jl. Contoh No. 1"', false)
            ->assertSee('id="modal-detail"', false)
            ->assertSee('bukaModalDetail', false);
    }

    public function test_supplier_detail_data_is_html_escaped(): void
    {
        Supplier::create([
            'name' => '<script>alert("supplier")</script>',
            'is_active' => false,
        ]);

        $response = $this->get(route('suppliers.index'));

        $response->assertOk()
            ->assertDontSee('<script>alert("supplier")</script>', false)
            ->assertSee('&lt;script&gt;alert(&quot;supplier&quot;)&lt;/script&gt;', false)
            ->assertSee('data-active="0"', false);
    }
}
