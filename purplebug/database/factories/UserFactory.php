<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'name'      => $this->faker->name(),
            'email'     => $this->faker->unique()->safeEmail(),
            'password'  => Hash::make('password'),
            'role'      => 'guest',
            'is_active' => true,
        ];
    }
}
