<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Services\SyncService;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected $customerService;
    protected $syncService;

    public function __construct(CustomerService $customerService, SyncService $syncService)
    {
        $this->customerService = $customerService;
        $this->syncService = $syncService;
    }

    public function index()
    {
        return view('members.index');
    }

    public function getCustomers(Request $request)
    {
        $query = Customer::query();

        if($request->filled('search')){

            $query->where(function($q) use ($request){

                $q->where('name','like','%'.$request->search.'%')
                ->orWhere('phone','like','%'.$request->search.'%');

            });

        }

        return response()->json([

            'success'=>true,
            'data'=>$query
                    ->orderBy('name')
                    ->get()

        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:15|regex:/^08\d+$/',
            'email'   => 'nullable|email',
            'address' => 'nullable|string'
        ]);

        $customer = $this->customerService->createCustomer($validated);

        $this->syncService->log(
            'Member',
            'Berhasil',
            "CREATE - Member baru berhasil dibuat: {$customer->name}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan',
            'data' => $customer
        ]);
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ],404);
        }

        return response()->json([
            'success'=>true,
            'data'=>$customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:15|regex:/^08\d+$/|unique:customers,phone,' . $id,
            'email'   => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer = $this->customerService->updateCustomer($id, $validated);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }

        $this->syncService->log(
            'Member',
            'Berhasil',
            "UPDATE - Data member berhasil diperbarui: {$customer->name}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil diperbarui',
            'data'    => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = $this->customerService->getCustomerById($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }

        $customerName = $customer->name;
        $customer->delete();

        $this->syncService->log(
            'Member',
            'Berhasil',
            "DELETE - Member berhasil dihapus: {$customerName}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
    }

    public function search(Request $request)
    {
        $keyword = trim($request->keyword);

        if (empty($keyword)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $customers = Customer::where('phone', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->orWhere('customer_code', 'like', "%{$keyword}%")
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
}