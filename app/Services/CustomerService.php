<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getAll()
    {
        return Customer::orderBy('name')->get();
    }

    public function search(string $query)
    {
        $q = trim($query);

        return Customer::where('is_active', true)
            ->where(function ($inner) use ($q) {
                $inner->where('name', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function findById($id): ?Customer
    {
        return Customer::find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update($id, array $data): ?Customer
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return null;
        }

        $customer->update($data);

        return $customer;
    }

    public function delete($id): bool
    {
        $customer = Customer::find($id);

        if (! $customer) {
            return false;
        }

        return $customer->delete();
    }
}