<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
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
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string'
        ]);

        $customer = $this->customerService->createCustomer($validated);

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
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string'
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diperbarui',
            'data' => $customer
        ]);
    }
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
    }
}