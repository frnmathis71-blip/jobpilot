<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'salary' => fake()->numberBetween(1500, 5000),
            'name' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
        ];
    }
}