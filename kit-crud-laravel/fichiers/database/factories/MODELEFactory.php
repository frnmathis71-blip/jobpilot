<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PARENT_MODELE;

class MODELEFactory extends Factory
{
    public function definition(): array
    {
        return [
            'CHAMP1' => fake()->name(),
            'CHAMP2' => fake()->numberBetween(1, 99),
            'PARENT_SINGULIER_id' => PARENT_MODELE::inRandomOrder()->first()->id,
        ];
    }
}
