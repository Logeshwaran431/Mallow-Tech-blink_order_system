<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Seed a known demo customer plus random customers.
     *
     * @return void
     */
    public function run()
    {
        Customer::updateOrCreate(
            ['email' => 'demo.customer@example.com'],
            ['name' => 'Demo Customer']
        );

        Customer::factory()->count(10)->create();
    }
}
