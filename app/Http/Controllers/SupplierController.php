<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function exportCsv(): StreamedResponse
    {
        $suppliers = Supplier::orderBy('name')->get();
        $fileName = 'supplier-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($suppliers) {
            $output = fopen('php://output', 'w');

            echo "\xEF\xBB\xBF";

            fputcsv($output, [
                'Nama Supplier',
                'Kontak Person',
                'Telepon',
                'Email',
                'Alamat',
                'Status',
            ]);

            foreach ($suppliers as $supplier) {
                fputcsv($output, [
                    $supplier->name,
                    $supplier->contact_person,
                    $supplier->phone,
                    $supplier->email,
                    $supplier->address,
                    $supplier->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
