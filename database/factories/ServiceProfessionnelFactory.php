<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceProfessionnel>
 */
class ServiceProfessionnelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement([
                'Consultation générale', 'Consultation spécialisée',
                'Échographie', 'Analyse sanguine', 'Radiographie',
                'Hospitalisation standard', 'Consultation urgence',
            ]),
            'description' => fake()->optional(0.7)->sentence(),
            'type' => fake()->randomElement(['consultation', 'examen', 'hospitalisation', 'chirurgie', 'urgence', 'autre']),
            'prix' => fake()->numberBetween(2000, 50000),
            'actif' => true,
        ];
    }
}
