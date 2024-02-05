<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => $this->faker->firstName(),
			'message' => $this->faker->firstName(),
        ];
    }
}
