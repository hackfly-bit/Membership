<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CabangFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_cabang' => $this->faker->firstName(),
			'code_cabang' => $this->faker->countryCode(),
			'alamat' => $this->faker->address(),
			// 'nohp' => $this->faker->firstName(),
            'nohp' => '081234567890',
        ];
    }
}
