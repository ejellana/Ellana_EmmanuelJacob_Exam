<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->words(3, true),
            'price'       => $this->faker->randomFloat(2, 100, 50000),
            'stocks'      => $this->faker->numberBetween(5, 100),
            'description' => $this->faker->sentence(),
        ];
    }
}
