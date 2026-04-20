<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\DossierProfessionnel> */
class DossierProfessionnelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'raison_sociale' => fake()->optional(0.6)->company(),
            'type_structure' => fake()->randomElement(['individuel', 'clinique', 'hopital', 'dispensaire', 'autre']),
            'specialite' => fake()->randomElement(['Médecine générale', 'Pédiatrie', 'Gynécologie', 'Cardiologie', 'Dentisterie']),
            'NIU' => strtoupper(fake()->bothify('??##########')),
            'forme_juridique' => fake()->optional(0.5)->randomElement(['SARL', 'SA', 'SAS', 'EI']),
            'statut' => 'en_attente',
            'numero_licence' => null,
            'statut_paiement_inscription' => 'en_attente',
        ];
    }

    public function valide(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'valide',
            'numero_licence' => 'PRO-'.now()->year.'-'.strtoupper(fake()->bothify('######')),
            'statut_paiement_inscription' => 'paye',
        ]);
    }

    public function recale(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'recale',
        ]);
    }
}
