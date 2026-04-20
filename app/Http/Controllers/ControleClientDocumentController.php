<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateControleClientDocumentRequest;
use App\Models\DossierMedical;
use App\Notifications\ActivationDecisionNotification;
use App\Services\IdentityComplianceReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ControleClientDocumentController extends Controller
{
    /**
     * Retourne les pieces du dossier et l'analyse de conformite IA.
     */
    public function show(DossierMedical $dossierMedical, IdentityComplianceReviewService $identityComplianceReviewService): JsonResponse
    {
        $review = $identityComplianceReviewService->reviewMedicalDossier($dossierMedical);

        return response()->json([
            'dossier_id' => $dossierMedical->id,
            'documents' => [
                'piece_identite_recto_path' => $dossierMedical->piece_identite_recto_path,
                'piece_identite_verso_path' => $dossierMedical->piece_identite_verso_path,
                'type_piece_identite' => $dossierMedical->type_piece_identite,
                'numero_piece_identite' => $dossierMedical->numero_piece_identite,
                'date_expiration_piece_identite' => $dossierMedical->date_expiration_piece_identite?->toDateString(),
            ],
            'validation' => [
                'statut' => $dossierMedical->documents_validation_statut,
                'ia_risk_level' => $review['risk_level'] ?? $dossierMedical->documents_validation_ia_risk_level,
                'ia_score' => $review['score'] ?? $dossierMedical->documents_validation_ia_score,
                'ia_reasons' => $review['reasons'] ?? $dossierMedical->documents_validation_ia_reasons,
                'source' => $review['source'] ?? 'local',
                'personnel_user_id' => $dossierMedical->documents_validation_personnel_user_id,
                'personnel_note' => $dossierMedical->documents_validation_personnel_note,
                'personnel_validated_at' => $dossierMedical->documents_validation_personnel_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Enregistre la decision finale du personnel apres controle des pieces.
     */
    public function validateDocuments(
        ValidateControleClientDocumentRequest $request,
        DossierMedical $dossierMedical,
        IdentityComplianceReviewService $identityComplianceReviewService
    ): JsonResponse {
        $review = $identityComplianceReviewService->reviewMedicalDossier($dossierMedical);
        $validated = $request->validated();

        $dossierMedical->update([
            'documents_validation_statut' => (string) $validated['decision'],
            'documents_validation_ia_risk_level' => (string) ($review['risk_level'] ?? 'low'),
            'documents_validation_ia_score' => (int) ($review['score'] ?? 0),
            'documents_validation_ia_reasons' => array_values((array) ($review['reasons'] ?? [])),
            'documents_validation_personnel_user_id' => Auth::id(),
            'documents_validation_personnel_note' => $validated['note_personnel'] ?? null,
            'documents_validation_personnel_at' => now(),
        ]);

        if ($dossierMedical->user) {
            $dossierMedical->user->notify(new ActivationDecisionNotification(
                profileType: 'patient',
                decision: (string) $validated['decision'],
                message: (string) ($validated['decision'] === 'valide'
                    ? 'Votre dossier patient est valide apres controle des pieces fournies.'
                    : 'Votre dossier patient est rejete apres controle des pieces. Veuillez corriger et renvoyer les documents demandes.'),
                dossierReference: (string) $dossierMedical->numero_unique,
                note: $validated['note_personnel'] ?? null,
                actionUrl: route('user.validation.status'),
            ));
        }

        return response()->json([
            'message' => 'Validation documentaire enregistree avec succes.',
            'validation' => [
                'statut' => $dossierMedical->documents_validation_statut,
                'ia_risk_level' => $dossierMedical->documents_validation_ia_risk_level,
                'ia_score' => $dossierMedical->documents_validation_ia_score,
                'ia_reasons' => $dossierMedical->documents_validation_ia_reasons,
                'personnel_user_id' => $dossierMedical->documents_validation_personnel_user_id,
                'personnel_note' => $dossierMedical->documents_validation_personnel_note,
                'personnel_validated_at' => $dossierMedical->documents_validation_personnel_at?->toDateTimeString(),
            ],
        ]);
    }
}
