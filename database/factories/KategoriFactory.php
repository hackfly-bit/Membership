<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kategory' => $this->faker->firstName(),
			// 'point' 50 or 100
            'point' => $this->faker->randomNumber([50, 100])


        ];
    }
}
