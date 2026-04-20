<?php

namespace Tests\Feature;

use App\Models\Frais;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DossierMedicalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer des données de test
        User::factory()->create(['role' => 'patient', 'name' => 'Patient Test']);
        User::factory()->create(['role' => 'professional', 'name' => 'Professional Test']);
        Frais::factory()->inscription()->create(['libelle' => 'Inscription Standard', 'prix' => 10000]);
    }

    public function test_dossier_medical_index_page_is_accessible()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/dossier-medicals');

        $response->assertStatus(200);
        $response->assertViewHas('dossiers');
    }

    public function test_dossier_medical_create_page_is_accessible()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/dossier-medicals/create');

        $response->assertStatus(200);
    }

    public function test_dossier_medical_can_be_created()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $frais = Frais::first();

        $dossierData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'date_naissance' => '1990-01-15',
            'sexe' => 'M',
            'telephone' => '+237 699 123 456',
            'adresse' => '123 Rue Test, Yaoundé',
            'source_creation' => 'guichet',
            'frais_id' => $frais->id,
            'statut_paiement_inscription' => 'paye',
            'mode_paiement_inscription' => 'cash',
        ];

        $response = $this->actingAs($user)->post('/dossier-medicals', $dossierData);

        $response->assertRedirect();
        $this->assertDatabaseHas('dossiers_medicaux', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
        ]);
    }
}
