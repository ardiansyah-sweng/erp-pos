<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Tampilkan daftar semua supplier.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('supplier.index', compact('suppliers'));
    }

    /**
     * Simpan supplier baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['nullable', 'string'],
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Perbarui data supplier yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['nullable', 'string'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        // Konversi input is_active menjadi boolean dengan benar
        $validated['is_active'] = $request->boolean('is_active');

        $supplier = Supplier::find($id);

        if (! $supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Hapus supplier dari database.
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        if (! $supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
