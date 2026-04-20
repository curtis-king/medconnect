<?php

namespace Database\Seeders;

use App\Models\ServiceMedical;
use Illuminate\Database\Seeder;

class ServiceMedicalSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['nom' => 'Prise de rendez-vous à domicile', 'type' => 'prise_rendez_vous', 'prix' => 5000, 'actif' => true],
            ['nom' => 'Téléconsultation', 'type' => 'teleconsultation', 'prix' => 3000, 'actif' => true],
            ['nom' => "Demande d'examen à domicile", 'type' => 'demande_examen', 'prix' => 10000, 'actif' => true],
            ['nom' => 'Prélèvement à domicile', 'type' => 'prelevement_domicile', 'prix' => 8000, 'actif' => true],
            ['nom' => 'Livraison de médicaments', 'type' => 'livraison_medicament', 'prix' => 2500, 'actif' => true],
            ['nom' => 'Hospitalisation à domicile (HAD)', 'type' => 'hospitalisation_domicile', 'prix' => 25000, 'actif' => true],
            ['nom' => 'Consultation', 'type' => 'consultation', 'prix' => 5000, 'actif' => true],
            ['nom' => 'Prescription', 'type' => 'prescription', 'prix' => 2000, 'actif' => true],
        ];

        foreach ($services as $service) {
            ServiceMedical::create($service);
        }
    }
}
