<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use Illuminate\Database\Seeder;

class BroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Broadcast::factory(10)->create();
    }
}
