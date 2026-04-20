<?php

namespace Tests\Unit;

use App\Models\ConsultationProfessionnelle;
use App\Models\OrdonnanceProfessionnelle;
use App\Services\TreatmentSupportService;
use Tests\TestCase;

class TreatmentSupportServiceTest extends TestCase
{
    public function test_patient_analysis_extracts_duration_from_ordonnance_lines(): void
    {
        config(['ai.providers.gemini.key' => null]);

        $service = new TreatmentSupportService;
        $ordonnance = new OrdonnanceProfessionnelle([
            'produits' => [
                'Amoxicilline 500mg - 3x/j pendant 7 jours',
                'Paracetamol 1g - si douleur pendant 3 jours',
            ],
            'prescription' => 'Traitement antibiotique puis antalgique.',
            'instructions_complementaires' => 'Prendre apres le repas.',
        ]);

        $analysis = $service->analyzeForPatient($ordonnance);

        $this->assertSame('analyse_locale', $analysis['source']);
        $this->assertSame(7, $analysis['duree_estimee_jours']);
        $this->assertCount(2, $analysis['prises']);
        $this->assertSame('7 jours', $analysis['prises'][0]['duree_texte']);
    }

    public function test_professional_suggestion_flags_missing_context_without_ai(): void
    {
        config(['ai.providers.gemini.key' => null]);

        $service = new TreatmentSupportService;
        $consultation = new ConsultationProfessionnelle;

        $suggestion = $service->suggestForProfessional($consultation, [
            'symptomes' => '',
            'diagnostic_medecin' => '',
            'diagnostic' => '',
            'ordonnance_produits' => '',
        ]);

        $this->assertSame('analyse_locale', $suggestion['source']);
        $this->assertNotEmpty($suggestion['points_attention']);
        $this->assertStringContainsString('insuffisantes', $suggestion['resume_clinique']);
    }
}
