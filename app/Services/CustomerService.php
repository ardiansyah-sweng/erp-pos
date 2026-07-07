<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function getCustomers()
    {
        return Customer::orderBy('name')->get();
    }

    public function createCustomer(array $data)
    {
        $last = Customer::latest()->first();

        $code = 'CUS0001';

        if ($last) {
            $number = (int) substr($last->customer_code, 3);
            $number++;

            $code = 'CUS' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }

        return Customer::create([
            'customer_code' => $code,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'points' => 0,
            'member_level' => 'Regular'
        ]);
    }

    public function getCustomerById($id)
    {
        return Customer::find($id);
    }

    public function addPoints(Customer $customer, $totalBelanja)
{
    // 1 poin setiap Rp10.000
    $points = floor($totalBelanja / 10000);

    $customer->points += $points;

    // Tentukan level member
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
}
}