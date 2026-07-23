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

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:semua,aktif,nonaktif'],
        ]);

        $search = trim((string) ($validated['search'] ?? ''));
        $status = (string) ($validated['status'] ?? 'semua');

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($status === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->get();

        $supplierSummary = [
            'total' => Supplier::count(),
            'aktif' => Supplier::where('is_active', true)->count(),
            'nonaktif' => Supplier::where('is_active', false)->count(),
        ];

        return view('supplier.index', compact(
            'suppliers',
            'search',
            'status',
            'supplierSummary',
        ));
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
