<?php
namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        static $counter = 0;
        $counter++;
        
        return [
            'customer_code' => 'CUS' . str_pad($counter, 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'points' => $this->faker->numberBetween(0, 1000),
            'member_level' => $this->faker->randomElement(['Regular', 'Silver', 'Gold'])
        ];
    }
}