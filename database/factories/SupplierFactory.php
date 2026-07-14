<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Model yang digunakan oleh factory ini.
     */
    protected $model = Supplier::class;

    /**
     * Definisi state default untuk model Supplier.
     * Data dummy disesuaikan dengan konteks bisnis Indonesia.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Prefix nama perusahaan supplier yang umum di Indonesia
        $prefixPerusahaan = $this->faker->randomElement([
            'PT', 'CV', 'UD', 'PD', 'Toko',
        ]);

        // Nama perusahaan dummy
        $namaPerusahaan = $prefixPerusahaan . ' ' . $this->faker->company();

        // Format nomor telepon Indonesia: 08xx-xxxx-xxxx
        $nomorTelepon = '08' . $this->faker->numerify('##-####-####');

        return [
            'name'           => $namaPerusahaan,
            'contact_person' => $this->faker->name(),
            'phone'          => $nomorTelepon,
            'email'          => $this->faker->unique()->safeEmail(),
            'address'        => $this->faker->address(),
            'is_active'      => true,
        ];
    }

    /**
     * State untuk supplier yang tidak aktif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
