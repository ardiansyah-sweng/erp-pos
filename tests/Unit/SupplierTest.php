<?php

namespace Tests\Unit;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test tabel suppliers ada setelah migration dijalankan.
     */
    public function test_suppliers_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('suppliers'));
    }

    /**
     * Test tabel suppliers memiliki kolom yang diharapkan.
     */
    public function test_suppliers_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('suppliers', [
            'id',
            'name',
            'contact_person',
            'phone',
            'email',
            'address',
            'is_active',
            'created_at',
            'updated_at',
        ]));
    }

    /**
     * Test atribut $fillable model Supplier sudah sesuai spesifikasi.
     */
    public function test_supplier_model_has_correct_fillable(): void
    {
        $supplier = new Supplier();

        $this->assertEquals(
            ['name', 'contact_person', 'phone', 'email', 'address', 'is_active'],
            $supplier->getFillable()
        );
    }

    /**
     * Test is_active di-cast menjadi boolean.
     */
    public function test_supplier_is_active_cast_to_boolean(): void
    {
        $supplier = Supplier::create([
            'name'      => 'Test Supplier',
            'is_active' => 1,
        ]);

        $this->assertIsBool($supplier->is_active);
        $this->assertTrue($supplier->is_active);
    }

    /**
     * Test nilai default is_active adalah true.
     * fresh() diperlukan agar nilai default dari database terbaca ke model.
     */
    public function test_supplier_is_active_defaults_to_true(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier Default',
        ])->fresh();

        $this->assertTrue($supplier->is_active);
    }

    /**
     * Test factory berhasil membuat data supplier yang valid.
     */
    public function test_supplier_factory_creates_valid_data(): void
    {
        $supplier = Supplier::factory()->create();

        $this->assertNotNull($supplier->id);
        $this->assertNotEmpty($supplier->name);
        $this->assertTrue($supplier->is_active);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test factory state inactive berhasil membuat supplier tidak aktif.
     */
    public function test_supplier_factory_inactive_state(): void
    {
        $supplier = Supplier::factory()->inactive()->create();

        $this->assertFalse($supplier->is_active);
    }

    /**
     * Test factory dapat membuat banyak supplier sekaligus.
     */
    public function test_supplier_factory_creates_multiple(): void
    {
        Supplier::factory()->count(10)->create();

        $this->assertDatabaseCount('suppliers', 10);
    }
}
