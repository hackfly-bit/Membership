<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'code' TRX -$year - $id
            'code' => 'TRX-' . date('Y') . '-' . $this->faker->randomNumber(5),
			'customer_id' => createOrRandomFactory(\App\Models\Customer::class),
			'tanggal' => $this->faker->dateTime(),
            'nominal' => $this->faker->numberBetween(100000, 1000000),
			'kategori_id' => createOrRandomFactory(\App\Models\Kategori::class),
			'keterangan' => $this->faker->text(),
        ];
    }
}
