<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => createOrRandomFactory(\App\Models\Customer::class),
			'point' => $this->faker->text(),
			'wd_reason' => $this->faker->firstName(),
        ];
    }
}
