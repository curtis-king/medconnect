<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DossierMedical>
 */
class DossierMedicalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_unique' => 'DM-'.date('Y').'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'source_creation' => $this->faker->randomElement(['guichet', 'en_ligne']),
            'actif' => $this->faker->boolean(90), // 90% de chance d'être actif
            'partage_actif' => $this->faker->boolean(20), // 20% de chance d'avoir le partage actif

            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'date_naissance' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'sexe' => $this->faker->randomElement(['M', 'F']),
            'telephone' => $this->faker->phoneNumber,
            'adresse' => $this->faker->address,

            'groupe_sanguin' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => $this->faker->optional(0.3)->sentence, // 30% de chance d'avoir des allergies
            'maladies_chroniques' => $this->faker->optional(0.2)->sentence, // 20% de chance d'avoir des maladies chroniques
            'traitements_en_cours' => $this->faker->optional(0.25)->sentence, // 25% de chance d'avoir des traitements
            'antecedents_familiaux' => $this->faker->optional(0.15)->sentence, // 15% de chance d'avoir des antécédents familiaux
            'antecedents_personnels' => $this->faker->optional(0.1)->sentence, // 10% de chance d'avoir des antécédents personnels

            'contact_urgence_nom' => $this->faker->name,
            'contact_urgence_telephone' => $this->faker->phoneNumber,
            'contact_urgence_relation' => $this->faker->randomElement(['Parent', 'Conjoint', 'Frère/Soeur', 'Ami', 'Autre']),

            'type_piece_identite' => $this->faker->randomElement(['cni', 'passeport', 'permis', 'autre']),
            'numero_piece_identite' => $this->faker->unique()->regexify('[A-Z0-9]{8,12}'),
            'date_expiration_piece_identite' => $this->faker->dateTimeBetween('now', '+10 years'),

            'frais_id' => \App\Models\Frais::where('type', 'inscription')->first()?->id,
            'statut_paiement_inscription' => $this->faker->randomElement(['en_attente', 'paye', 'exonere']),
            'mode_paiement_inscription' => $this->faker->randomElement(['cash', 'en_ligne', 'mobile_money', 'carte']),
            'reference_paiement_inscription' => $this->faker->optional(0.7)->uuid, // 70% de chance d'avoir une référence
        ];
    }

    /**
     * Indicate that the dossier is actif.
     */
    public function actif(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => true,
        ]);
    }

    /**
     * Indicate that the dossier is inactif.
     */
    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * Indicate that the paiement is paye.
     */
    public function paye(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_paiement_inscription' => 'paye',
        ]);
    }

    /**
     * Indicate that the paiement is en attente.
     */
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_paiement_inscription' => 'en_attente',
        ]);
    }
}
