<?php

namespace Database\Seeders;

use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoProfessionalAndMedicalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professionalUser = User::updateOrCreate(
            ['email' => 'filaldimitri@gmail.com'],
            [
                'name' => 'Filal Dimitri',
                'password' => Hash::make('admin123456789'),
                'role' => User::ROLE_PROFESSIONAL,
                'status' => User::STATUS_ACTIVE,
                'profile' => 'Professionnel de sante',
            ]
        );

        DossierProfessionnel::updateOrCreate(
            ['user_id' => $professionalUser->id],
            [
                'raison_sociale' => 'azer',
                'type_structure' => 'individuel',
                'specialite' => 'Generaliste',
                'statut' => 'valide',
                'statut_paiement_inscription' => 'paye',
            ]
        );

        $medicalUser = User::updateOrCreate(
            ['email' => 'amir@gmail.com'],
            [
                'name' => 'Amir',
                'password' => Hash::make('admin123456789'),
                'role' => User::ROLE_USER,
                'status' => User::STATUS_ACTIVE,
                'profile' => 'Membre patient',
            ]
        );

        $medicalDossier = DossierMedical::where('user_id', $medicalUser->id)->first();

        if (! $medicalDossier) {
            DossierMedical::create([
                'user_id' => $medicalUser->id,
                'numero_unique' => 'DM-DEMO-'.Str::upper(Str::random(8)),
                'source_creation' => 'en_ligne',
                'actif' => true,
                'partage_actif' => false,
                'nom' => 'Amir',
                'prenom' => 'Amir',
                'sexe' => 'M',
                'telephone' => '+237699000111',
                'statut_paiement_inscription' => 'paye',
            ]);
        } else {
            $medicalDossier->update([
                'source_creation' => 'en_ligne',
                'actif' => true,
                'partage_actif' => false,
                'nom' => 'Amir',
                'prenom' => 'Amir',
                'sexe' => 'M',
                'telephone' => '+237699000111',
                'statut_paiement_inscription' => 'paye',
            ]);
        }
    }
}
