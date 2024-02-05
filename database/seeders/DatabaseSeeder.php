<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cabang;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
        ]);
        
        Cabang::factory(10)->create();
        Customer::factory(10)->create();
        Kategori::factory(10)->create();
        Transaksi::factory(10)->create();
    }
}
