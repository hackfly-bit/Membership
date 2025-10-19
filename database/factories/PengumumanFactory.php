<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PengumumanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => $this->faker->firstName(),
			'isi' => $this->faker->text(),
        ];
    }
}
