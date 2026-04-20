<?php

namespace Database\Factories;

use App\Models\Frais;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Frais>
 */
class FraisFactory extends Factory
{
    protected $model = Frais::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => fake()->randomElement(['Standard', 'Premium', 'Basic', 'VIP']),
            'prix' => fake()->randomElement([5000, 10000, 15000, 20000, 25000]),
            'type' => fake()->randomElement(['inscription', 'reabonnement', 'contribution']),
            'detail' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State for inscription type.
     */
    public function inscription(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'inscription',
            'libelle' => 'Frais d\'inscription',
        ]);
    }

    /**
     * State for reabonnement type.
     */
    public function reabonnement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reabonnement',
            'libelle' => 'Abonnement mensuel',
        ]);
    }
}
