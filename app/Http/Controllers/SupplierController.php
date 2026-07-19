<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SyncService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('supplier.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['nullable', 'string'],
        ]);

        $supplier = Supplier::create($validated);

        $this->syncService->log(
            'Supplier',
            'Berhasil',
            "CREATE - Supplier berhasil ditambahkan: {$supplier->name}"
        );

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

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

        $validated['is_active'] = $request->boolean('is_active');

        $supplier = Supplier::find($id);

        if (! $supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $supplier->update($validated);

        $this->syncService->log(
            'Supplier',
            'Berhasil',
            "UPDATE - Supplier berhasil diperbarui: {$supplier->name}"
        );

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        if (! $supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Supplier tidak ditemukan.');
        }

        $supplierName = $supplier->name;

        $supplier->delete();

        $this->syncService->log(
            'Supplier',
            'Berhasil',
            "DELETE - Supplier berhasil dihapus: {$supplierName}"
        );

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}