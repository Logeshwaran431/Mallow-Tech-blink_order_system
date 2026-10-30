<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed a fixed catalogue plus some random and low-stock products.
     *
     * @return void
     */
    public function run()
    {
        $catalogue = [
            ['name' => 'Arabica Coffee Beans 1kg', 'code' => 'COF-001', 'price' => '45.90', 'tax_percentage' => '6.00', 'stock_on_hand' => 120],
            ['name' => 'Green Tea 50 Bags', 'code' => 'TEA-001', 'price' => '12.50', 'tax_percentage' => '6.00', 'stock_on_hand' => 80],
            ['name' => 'Paper Cup 8oz (100 pcs)', 'code' => 'CUP-008', 'price' => '18.00', 'tax_percentage' => '8.00', 'stock_on_hand' => 8],
            ['name' => 'Oat Milk 1L', 'code' => 'MLK-OAT', 'price' => '9.80', 'tax_percentage' => '0.00', 'stock_on_hand' => 3],
            ['name' => 'Chocolate Syrup 750ml', 'code' => 'SYR-CHO', 'price' => '24.00', 'tax_percentage' => '8.00', 'stock_on_hand' => 0],
        ];

        foreach ($catalogue as $product) {
            Product::updateOrCreate(['code' => $product['code']], $product);
        }

        Product::factory()->count(15)->create();
        Product::factory()->count(3)->lowStock()->create();
    }
}
