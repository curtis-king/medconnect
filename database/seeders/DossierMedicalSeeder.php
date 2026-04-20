<?php

namespace Database\Seeders;

use App\Models\DossierMedical;
use App\Models\Frais;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class DossierMedicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les frais
        $fraisInscription = Frais::where('type', 'inscription')->first();
        $fraisReabonnement = Frais::where('type', 'reabonnement')->first();

        if (! $fraisInscription) {
            $this->command->warn('Aucun frais d\'inscription trouvé. Exécutez FraisSeeder d\'abord.');

            return;
        }

        // Créer des dossiers médicaux avec différents statuts
        $dossiers = [
            // Clients payés et actifs
            [
                'nom' => 'Mbarga',
                'prenom' => 'Jean-Pierre',
                'sexe' => 'M',
                'telephone' => '+237 699 123 456',
                'statut_paiement_inscription' => 'paye',
                'actif' => true,
            ],
            [
                'nom' => 'Ngo Bassa',
                'prenom' => 'Marie-Claire',
                'sexe' => 'F',
                'telephone' => '+237 677 234 567',
                'statut_paiement_inscription' => 'paye',
                'actif' => true,
            ],
            [
                'nom' => 'Fotso',
                'prenom' => 'Emmanuel',
                'sexe' => 'M',
                'telephone' => '+237 655 345 678',
                'statut_paiement_inscription' => 'paye',
                'actif' => true,
            ],
            // Clients en attente
            [
                'nom' => 'Tchana',
                'prenom' => 'Pauline',
                'sexe' => 'F',
                'telephone' => '+237 690 456 789',
                'statut_paiement_inscription' => 'en_attente',
                'actif' => false,
            ],
            [
                'nom' => 'Ondoua',
                'prenom' => 'Samuel',
                'sexe' => 'M',
                'telephone' => '+237 678 567 890',
                'statut_paiement_inscription' => 'en_attente',
                'actif' => false,
            ],
            // Client exonéré
            [
                'nom' => 'Messi',
                'prenom' => 'Thérèse',
                'sexe' => 'F',
                'telephone' => '+237 656 678 901',
                'statut_paiement_inscription' => 'exonere',
                'actif' => true,
            ],
        ];

        foreach ($dossiers as $index => $data) {
            $numeroUnique = 'DM-'.date('Y').'-'.str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            // Skip if already exists
            if (DossierMedical::where('numero_unique', $numeroUnique)->exists()) {
                continue;
            }

            $dossier = DossierMedical::factory()->create(array_merge($data, [
                'numero_unique' => $numeroUnique,
                'frais_id' => $fraisInscription->id,
                'date_naissance' => fake()->dateTimeBetween('-60 years', '-18 years'),
                'adresse' => fake()->randomElement(['Yaoundé', 'Douala', 'Bafoussam', 'Garoua', 'Bamenda']),
                'groupe_sanguin' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            ]));

            // Créer une subscription active pour les clients payés
            if ($data['statut_paiement_inscription'] === 'paye' && $fraisReabonnement) {
                Subscription::create([
                    'dossier_medical_id' => $dossier->id,
                    'frais_id' => $fraisReabonnement->id,
                    'date_debut' => now(),
                    'date_fin' => now()->addMonth(),
                    'nombre_mois' => 1,
                    'montant' => 0, // Premier mois offert
                    'statut' => 'actif',
                    'mode_paiement' => 'offert',
                    'date_paiement' => now(),
                    'notes' => 'Premier mois offert à l\'inscription',
                ]);
            }
        }

        // Créer 10 dossiers supplémentaires aléatoires via la factory
        DossierMedical::factory(10)->create([
            'frais_id' => $fraisInscription->id,
        ]);
    }
}
