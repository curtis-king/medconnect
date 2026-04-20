<?php

namespace Database\Factories;

use App\Models\TauxReduction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TauxReduction>
 */
class TauxReductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => fake()->sentence(3),
            'taux' => fake()->randomFloat(2, 0, 100),
            'type' => fake()->randomElement(array_keys(TauxReduction::TYPES)),
            'detail' => fake()->optional()->paragraph(),
            'actif' => fake()->boolean(),
        ];
    }
}
