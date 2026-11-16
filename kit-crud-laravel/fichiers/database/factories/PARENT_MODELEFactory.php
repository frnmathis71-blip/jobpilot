<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PARENT_MODELEFactory extends Factory
{
    public function definition(): array
    {
        return [
            'PARENT_CHAMP' => fake()->name(),
        ];
    }
}
