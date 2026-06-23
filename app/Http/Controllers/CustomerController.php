<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAll();

        return view('customer.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $this->customerService->create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $result = $this->customerService->update($id, $validated);

        if (! $result) {
            return redirect()->route('customers.index')
                ->with('error', 'Pelanggan tidak ditemukan.');
        }

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $result = $this->customerService->delete($id);

        if (! $result) {
            return redirect()->route('customers.index')
                ->with('error', 'Pelanggan tidak ditemukan.');
        }

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');

        if (mb_strlen(trim($query)) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $customers = $this->customerService->search($query);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }
}