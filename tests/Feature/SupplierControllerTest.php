<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // INDEX
    // =========================================================

    /**
     * Test halaman index supplier mengembalikan status 200.
     */
    public function test_supplier_index_returns_successful_response(): void
    {
        $response = $this->get('/suppliers');

        $response->assertStatus(200);
    }

    /**
     * Test halaman index supplier menampilkan view yang benar.
     */
    public function test_supplier_index_returns_correct_view(): void
    {
        $response = $this->get('/suppliers');

        $response->assertViewIs('supplier.index');
    }

    /**
     * Test halaman index supplier meneruskan data suppliers ke view.
     */
    public function test_supplier_index_passes_suppliers_data_to_view(): void
    {
        Supplier::create(['name' => 'PT Maju Bersama']);

        $response = $this->get('/suppliers');

        $response->assertViewHas('suppliers');
        $this->assertCount(1, $response->viewData('suppliers'));
    }

    /**
     * Test halaman index berfungsi saat database kosong.
     */
    public function test_supplier_index_works_with_empty_database(): void
    {
        $response = $this->get('/suppliers');

        $response->assertStatus(200);
        $response->assertViewHas('suppliers');
        $this->assertCount(0, $response->viewData('suppliers'));
    }

    public function test_supplier_index_can_search_supplier_details(): void
    {
        Supplier::create([
            'name' => 'PT Sumber Makmur',
            'contact_person' => 'Budi',
            'phone' => '08123456789',
            'email' => 'budi@sumber.test',
        ]);
        Supplier::create([
            'name' => 'CV Berkah',
            'contact_person' => 'Siti',
            'phone' => '08987654321',
            'email' => 'siti@berkah.test',
        ]);

        $response = $this->get('/suppliers?search=081234');

        $response->assertOk()
            ->assertViewHas('search', '081234')
            ->assertSee('PT Sumber Makmur')
            ->assertDontSee('CV Berkah');
    }

    public function test_supplier_index_can_filter_by_active_status(): void
    {
        Supplier::create(['name' => 'Supplier Aktif', 'is_active' => true]);
        Supplier::create(['name' => 'Supplier Nonaktif', 'is_active' => false]);

        $response = $this->get('/suppliers?status=aktif');

        $response->assertOk()
            ->assertViewHas('status', 'aktif')
            ->assertSee('Supplier Aktif')
            ->assertDontSee('Supplier Nonaktif');
    }

    public function test_supplier_search_and_status_filter_can_be_combined(): void
    {
        Supplier::create(['name' => 'Maju Aktif', 'is_active' => true]);
        Supplier::create(['name' => 'Maju Nonaktif', 'is_active' => false]);
        Supplier::create(['name' => 'Berkah Aktif', 'is_active' => true]);

        $response = $this->get('/suppliers?search=Maju&status=nonaktif');

        $response->assertOk()
            ->assertSee('Maju Nonaktif')
            ->assertDontSee('Maju Aktif')
            ->assertDontSee('Berkah Aktif');
    }

    public function test_supplier_summary_is_not_affected_by_filters(): void
    {
        Supplier::create(['name' => 'Supplier Aktif', 'is_active' => true]);
        Supplier::create(['name' => 'Supplier Nonaktif', 'is_active' => false]);

        $response = $this->get('/suppliers?status=aktif');

        $response->assertViewHas('supplierSummary', [
            'total' => 2,
            'aktif' => 1,
            'nonaktif' => 1,
        ]);
    }

    // =========================================================
    // STORE
    // =========================================================

    /**
     * Test simpan supplier baru berhasil dengan data lengkap.
     */
    public function test_store_supplier_berhasil(): void
    {
        $response = $this->post('/suppliers', [
            'name'           => 'PT Sejahtera Abadi',
            'contact_person' => 'Budi Santoso',
            'phone'          => '0812-3456-7890',
            'email'          => 'budi@sejahtera.co.id',
            'address'        => 'Jl. Merdeka No. 1, Jakarta',
        ]);

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Sejahtera Abadi']);
    }

    /**
     * Test simpan supplier berhasil hanya dengan field wajib (name).
     */
    public function test_store_supplier_berhasil_dengan_field_minimal(): void
    {
        $response = $this->post('/suppliers', [
            'name' => 'CV Karya Mandiri',
        ]);

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', ['name' => 'CV Karya Mandiri']);
    }

    /**
     * Test validasi gagal — name kosong.
     */
    public function test_store_supplier_validation_fails_without_name(): void
    {
        $response = $this->post('/suppliers', []);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test validasi gagal — format email tidak valid.
     */
    public function test_store_supplier_validation_fails_with_invalid_email(): void
    {
        $response = $this->post('/suppliers', [
            'name'  => 'PT Test Gagal',
            'email' => 'bukan-email-valid',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test validasi gagal — name melebihi 255 karakter.
     */
    public function test_store_supplier_validation_fails_with_name_too_long(): void
    {
        $response = $this->post('/suppliers', [
            'name' => str_repeat('A', 256),
        ]);

        $response->assertSessionHasErrors('name');
    }

    // =========================================================
    // UPDATE
    // =========================================================

    /**
     * Test update supplier berhasil.
     */
    public function test_update_supplier_berhasil(): void
    {
        $supplier = Supplier::create(['name' => 'Nama Lama']);

        $response = $this->put('/suppliers/' . $supplier->id, [
            'name'  => 'Nama Baru',
            'phone' => '0821-9999-0000',
        ]);

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suppliers', [
            'id'   => $supplier->id,
            'name' => 'Nama Baru',
        ]);
    }

    /**
     * Test update supplier dapat mengubah status is_active.
     */
    public function test_update_supplier_dapat_ubah_is_active(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier Aktif', 'is_active' => true]);

        $response = $this->put('/suppliers/' . $supplier->id, [
            'name'      => 'Supplier Aktif',
            'is_active' => false,
        ]);

        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', [
            'id'        => $supplier->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test update supplier gagal — supplier tidak ditemukan.
     */
    public function test_update_supplier_not_found(): void
    {
        $response = $this->put('/suppliers/999999', [
            'name' => 'Nama Baru',
        ]);

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('error');
    }

    /**
     * Test validasi update gagal — name kosong.
     */
    public function test_update_supplier_validation_fails_without_name(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier Ada']);

        $response = $this->put('/suppliers/' . $supplier->id, [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test validasi update gagal — format email tidak valid.
     */
    public function test_update_supplier_validation_fails_with_invalid_email(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier Ada']);

        $response = $this->put('/suppliers/' . $supplier->id, [
            'name'  => 'Supplier Ada',
            'email' => 'email-tidak-valid',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // =========================================================
    // DESTROY
    // =========================================================

    /**
     * Test hapus supplier berhasil.
     */
    public function test_destroy_supplier_berhasil(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier Hapus']);

        $response = $this->delete('/suppliers/' . $supplier->id);

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test hapus supplier gagal — supplier tidak ditemukan.
     */
    public function test_destroy_supplier_not_found(): void
    {
        $response = $this->delete('/suppliers/999999');

        $response->assertRedirect('/suppliers');
        $response->assertSessionHas('error');
    }
}
