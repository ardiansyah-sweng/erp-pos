<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getAll()
    {
        return Customer::orderBy('name')->get();
    }

    public function getCustomers()
    {
        return $this->getAll();
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

    public function getCustomerById($id)
    {
        return $this->findById($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function createCustomer(array $data)
    {
        $last = Customer::latest()->first();
        $code = 'CUS0001';

        if ($last) {
            $number = (int) substr((string) $last->customer_code, 3);
            $number++;
            $code = 'CUS' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
        }

        $data = array_merge($data, [
            'customer_code' => $code,
            'points' => 0,
            'member_level' => 'Regular',
        ]);

        return $this->create($data);
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

    public function addPoints(Customer $customer, $totalBelanja)
    {
        $points = intdiv((int) $totalBelanja, 10000);
        $customer->points += $points;

        if ($customer->points >= 3000) {
            $customer->member_level = 'Platinum';
        } elseif ($customer->points >= 1500) {
            $customer->member_level = 'Gold';
        } elseif ($customer->points >= 500) {
            $customer->member_level = 'Silver';
        } else {
            $customer->member_level = 'Regular';
        }

        $customer->save();

        return $customer;
    }
}