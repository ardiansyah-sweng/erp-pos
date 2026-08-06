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

    public function exportCsv(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('search', ''));

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return response()->streamDownload(function () use ($suppliers): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Nama Supplier',
                'Kontak Person',
                'Telepon',
                'Email',
                'Alamat',
                'Status',
            ], ',', '"', '');

            foreach ($suppliers as $supplier) {
                fputcsv($output, [
                    $supplier->name,
                    $supplier->contact_person,
                    $supplier->phone,
                    $supplier->email,
                    $supplier->address,
                    $supplier->is_active ? 'Aktif' : 'Nonaktif',
                ], ',', '"', '');
            }

            fclose($output);
        }, 'supplier-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}