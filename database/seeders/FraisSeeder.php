<?php

namespace Database\Seeders;

use App\Models\Frais;
use Illuminate\Database\Seeder;

class FraisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frais = [
            [
                'libelle' => 'Inscription Standard',
                'prix' => 2500,
                'type' => 'inscription',
                'detail' => 'Frais d\'inscription standard pour nouveau client',
            ],
            [
                'libelle' => 'Inscription VIP',
                'prix' => 5000,
                'type' => 'inscription',
                'detail' => 'Frais d\'inscription VIP avec services premium',
            ],
            [
                'libelle' => 'Réabonnement Mensuel',
                'prix' => 1500,
                'type' => 'reabonnement',
                'detail' => 'Frais de réabonnement mensuel standard',
            ],
            [
                'libelle' => 'Contribution Annuelle',
                'prix' => 15000,
                'type' => 'contribution',
                'detail' => 'Contribution annuelle pour les services complets',
            ],
            [
                'libelle' => 'Inscription Professionnelle',
                'prix' => 10000,
                'type' => 'inscription_pro',
                'detail' => 'Frais d\'inscription pour devenir professionnel sur la plateforme',
            ],
            [
                'libelle' => 'Réabonnement Professionnel Mensuel',
                'prix' => 5000,
                'type' => 'reabonnement_pro',
                'detail' => 'Abonnement mensuel pour maintenir l\'accès professionnel',
            ],
        ];

        foreach ($frais as $f) {
            Frais::updateOrCreate(
                ['libelle' => $f['libelle'], 'type' => $f['type']],
                $f
            );
        }
    }
}
