<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FraisInscription>
 */
class FraisInscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->randomElement([
                'Inscription de base',
                'Inscription premium',
                'Inscription annuelle',
                'Inscription étudiante',
                'Inscription senior',
            ]),
            'montant' => $this->faker->randomFloat(2, 10, 500),
            'detail' => $this->faker->optional(0.7)->sentence, // 70% de chance d'avoir un détail
        ];
    }
}
