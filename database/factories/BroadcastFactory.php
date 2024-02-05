<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BroadcastFactory extends Factory
{
    public function definition(): array
    {
        return [
            'api_status' => $this->faker->firstName(),
			'detail' => $this->faker->firstName(),
			'process' => $this->faker->firstName(),
			'status' => $this->faker->firstName(),
			'target' => $this->faker->firstName(),
			'reason' => $this->faker->firstName(),
        ];
    }
}
