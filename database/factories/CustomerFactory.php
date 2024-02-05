<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->firstName(),
			'nohp' => $this->faker->phoneNumber(),
			'email' => $this->faker->safeEmail(),
			'role' => 'customer', // 'admin', 'user
			'cabang_id' => createOrRandomFactory(\App\Models\Cabang::class),
        ];
    }
}
