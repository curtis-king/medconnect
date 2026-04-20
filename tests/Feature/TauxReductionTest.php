<?php

namespace Tests\Feature;

use App\Models\TauxReduction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TauxReductionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_view_taux_reductions_index()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('taux-reductions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('taux-reductions.index');
    }

    public function test_user_can_create_taux_reduction()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $data = [
            'libelle' => 'Réduction Étudiant',
            'taux' => 15.50,
            'type' => 'inscription',
            'detail' => 'Réduction pour les étudiants',
            'actif' => true,
        ];

        $response = $this->actingAs($user)->post(route('taux-reductions.store'), $data);

        $response->assertRedirect(route('taux-reductions.index'));
        $this->assertDatabaseHas('taux_reductions', $data);
    }

    public function test_user_can_view_taux_reduction()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $tauxReduction = TauxReduction::factory()->create();

        $response = $this->actingAs($user)->get(route('taux-reductions.show', $tauxReduction));

        $response->assertStatus(200);
        $response->assertViewIs('taux-reductions.show');
        $response->assertViewHas('tauxReduction', $tauxReduction);
    }

    public function test_user_can_update_taux_reduction()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $tauxReduction = TauxReduction::factory()->create();

        $data = [
            'libelle' => 'Réduction Senior',
            'taux' => 20.00,
            'type' => 'reabonnement',
            'detail' => 'Réduction pour les seniors',
            'actif' => false,
        ];

        $response = $this->actingAs($user)->put(route('taux-reductions.update', $tauxReduction), $data);

        $response->assertRedirect(route('taux-reductions.index'));
        $this->assertDatabaseHas('taux_reductions', $data);
    }

    public function test_user_can_delete_taux_reduction()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $tauxReduction = TauxReduction::factory()->create();

        $response = $this->actingAs($user)->delete(route('taux-reductions.destroy', $tauxReduction));

        $response->assertRedirect(route('taux-reductions.index'));
        $this->assertDatabaseMissing('taux_reductions', ['id' => $tauxReduction->id]);
    }

    public function test_taux_reduction_validation_rules()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $data = [
            'libelle' => '',
            'taux' => 150, // Invalid: should be <= 100
            'type' => 'invalid_type',
            'actif' => 'not_boolean',
        ];

        $response = $this->actingAs($user)->post(route('taux-reductions.store'), $data);

        $response->assertSessionHasErrors(['libelle', 'taux', 'type', 'actif']);
    }
}
