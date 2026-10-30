<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => ucwords($this->faker->unique()->words(2, true)),
            'code' => strtoupper($this->faker->unique()->bothify('PRD-####??')),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'tax_percentage' => $this->faker->randomElement([0, 6, 8, 10]),
            'stock_on_hand' => $this->faker->numberBetween(20, 200),
        ];
    }

    /**
     * Indicate that the product is low on stock.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function lowStock()
    {
        return $this->state(function () {
            return [
                'stock_on_hand' => $this->faker->numberBetween(0, 5),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function outOfStock()
    {
        return $this->state(function () {
            return [
                'stock_on_hand' => 0,
            ];
        });
    }
}
