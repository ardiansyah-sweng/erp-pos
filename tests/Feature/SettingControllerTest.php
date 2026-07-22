<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_displays_default_values(): void
    {
        $this->get(route('settings.index'))
            ->assertOk()
            ->assertViewIs('settings.index')
            ->assertSee('Pengaturan Toko')
            ->assertSee('ERP POS')
            ->assertSee('Diskon')
            ->assertSee('Biaya Parkir')
            ->assertSee('Uang Dibayar')
            ->assertSee('Kembalian');

        $this->assertDatabaseCount('store_settings', 0);
    }

    public function test_settings_can_be_saved(): void
    {
        $response = $this->put(route('settings.update'), [
            'store_name' => 'Toko Nusantara',
            'address' => 'Jl. Merdeka No. 10',
            'phone' => '081234567890',
            'receipt_footer' => 'Terima kasih dan sampai jumpa.',
            'low_stock_notification' => '1',
        ]);

        $response->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('store_settings', [
            'store_name' => 'Toko Nusantara',
            'currency' => 'IDR',
            'low_stock_notification' => true,
        ]);
    }

    public function test_saving_again_updates_the_single_settings_record(): void
    {
        StoreSetting::create(StoreSetting::defaults());

        $this->put(route('settings.update'), [
            'store_name' => 'Nama Baru',
        ])->assertRedirect(route('settings.index'));

        $this->assertDatabaseCount('store_settings', 1);
        $this->assertDatabaseHas('store_settings', [
            'store_name' => 'Nama Baru',
            'currency' => 'IDR',
            'low_stock_notification' => false,
        ]);
    }

    public function test_settings_always_use_idr_even_when_another_currency_is_submitted(): void
    {
        $this->put(route('settings.update'), [
            'store_name' => 'Toko Lokal',
            'currency' => 'USD',
        ])->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('store_settings', [
            'store_name' => 'Toko Lokal',
            'currency' => 'IDR',
        ]);
    }

    public function test_settings_validation_rejects_invalid_store_name(): void
    {
        $this->from(route('settings.index'))
            ->put(route('settings.update'), [
                'store_name' => '',
            ])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors(['store_name']);

        $this->assertDatabaseCount('store_settings', 0);
    }
}
