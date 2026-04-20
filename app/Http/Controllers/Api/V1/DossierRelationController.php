<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DossierMedical;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DossierRelationController extends Controller
{
    /**
     * Declare as dependent (personne à charge) of another user.
     */
    public function declarePersonneACharge(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lien_avec_responsable' => 'required|string|in:enfant,conjoint,parent,frere_soeur,autre',
            'responsable_nom' => 'nullable|string|max:255',
            'responsable_prenom' => 'nullable|string|max:255',
            'responsable_telephone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dossier->est_personne_a_charge = true;
        $dossier->lien_avec_responsable = $request->lien_avec_responsable;
        $dossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Déclaration de personne à charge enregistrée',
            'dossier' => [
                'est_personne_a_charge' => $dossier->est_personne_a_charge,
                'lien_avec_responsable' => $dossier->lien_avec_responsable,
                'lien_avec_responsable_label' => $dossier->lien_avec_responsable_label,
            ],
        ], 200);
    }

    /**
     * Declare employer for subscription.
     */
    public function declareEmployeur(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'employeur_nom' => 'required|string|max:255',
            'employeur_adresse' => 'nullable|string|max:500',
            'employeur_telephone' => 'nullable|string|max:20',
            'employeur_email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dossier->employeur_nom = $request->employeur_nom;
        $dossier->employeur_adresse = $request->employeur_adresse;
        $dossier->employeur_telephone = $request->employeur_telephone;
        $dossier->employeur_email = $request->employeur_email;
        $dossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Employeur enregistré',
            'dossier' => [
                'employeur_nom' => $dossier->employeur_nom,
                'employeur_adresse' => $dossier->employeur_adresse,
                'employeur_telephone' => $dossier->employeur_telephone,
                'employeur_email' => $dossier->employeur_email,
            ],
        ], 200);
    }

    /**
     * Declare as standalone (not dependent).
     */
    public function declareStandalone(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $dossier->est_personne_a_charge = false;
        $dossier->lien_avec_responsable = null;
        $dossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Statut indépendant enregistré',
            'dossier' => [
                'est_personne_a_charge' => $dossier->est_personne_a_charge,
            ],
        ], 200);
    }

    /**
     * Get all relations info for current dossier.
     */
    public function getRelations(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'relations' => [
                'est_personne_a_charge' => $dossier->est_personne_a_charge,
                'lien_avec_responsable' => $dossier->lien_avec_responsable,
                'lien_avec_responsable_label' => $dossier->lien_avec_responsable_label,
                'employeur_nom' => $dossier->employeur_nom,
                'employeur_adresse' => $dossier->employeur_adresse,
                'employeur_telephone' => $dossier->employeur_telephone,
                'employeur_email' => $dossier->employeur_email,
            ],
        ], 200);
    }

    /**
     * Get all dependents linked to current user.
     */
    public function getDependents(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $dependents = DossierMedical::where('responsable_user_id', $user->id)
            ->where('actif', true)
            ->get()
            ->map(function ($dep) {
                return [
                    'id' => $dep->id,
                    'numero_unique' => $dep->numero_unique,
                    'nom' => $dep->nom,
                    'prenom' => $dep->prenom,
                    'lien_avec_responsable' => $dep->lien_avec_responsable,
                    'lien_avec_responsable_label' => $dep->lien_avec_responsable_label,
                    'photo_profil_url' => $dep->photo_profil_path ? asset('storage/'.$dep->photo_profil_path) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'dependents' => $dependents,
        ], 200);
    }

    /**
     * Add a dependent dossier by number.
     */
    public function addDependent(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical principal trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_dossier' => 'required|string|max:50',
            'lien_avec_responsable' => 'required|string|in:enfant,conjoint,parent,frere_soeur,autre',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dependentDossier = DossierMedical::where('numero_unique', $request->numero_dossier)
            ->first();

        if (! $dependentDossier) {
            return response()->json([
                'success' => false,
                'message' => 'Dossier médical non trouvé',
            ], 404);
        }

        if ($dependentDossier->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas vous ajouter vous-même',
            ], 400);
        }

        if ($dependentDossier->responsable_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce dossier est déjà lié à un responsable',
            ], 400);
        }

        $dependentDossier->responsable_user_id = $user->id;
        $dependentDossier->est_personne_a_charge = true;
        $dependentDossier->lien_avec_responsable = $request->lien_avec_responsable;
        $dependentDossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Personne à charge ajoutée',
            'dependent' => [
                'id' => $dependentDossier->id,
                'numero_unique' => $dependentDossier->numero_unique,
                'nom' => $dependentDossier->nom,
                'prenom' => $dependentDossier->prenom,
                'lien_avec_responsable' => $dependentDossier->lien_avec_responsable,
                'lien_avec_responsable_label' => $dependentDossier->lien_avec_responsable_label,
            ],
        ], 200);
    }

    /**
     * Remove a dependent.
     */
    public function removeDependent(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'dependent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dependentDossier = DossierMedical::where('id', $request->dependent_id)
            ->where('responsable_user_id', $user->id)
            ->first();

        if (! $dependentDossier) {
            return response()->json([
                'success' => false,
                'message' => 'Personne à charge non trouvée',
            ], 404);
        }

        $dependentDossier->responsable_user_id = null;
        $dependentDossier->est_personne_a_charge = false;
        $dependentDossier->lien_avec_responsable = null;
        $dependentDossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Personne à charge supprimée',
        ], 200);
    }

    /**
     * Get subscription statistics for current user.
     */
    public function getSubscriptionStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $dependentsCount = DossierMedical::where('responsable_user_id', $user->id)
            ->where('actif', true)
            ->count();

        $mainSubscription = $dossier->activeSubscription;
        $mainFrais = $mainSubscription ? $mainSubscription->frais : $dossier->frais;

        $monthlyPrice = $mainFrais ? (float) $mainFrais->prix : 0;
        $totalPrice = $monthlyPrice * ($dependentsCount + 1);

        return response()->json([
            'success' => true,
            'stats' => [
                'dependents_count' => $dependentsCount,
                'total_persons' => $dependentsCount + 1,
                'monthly_price' => $monthlyPrice,
                'monthly_total' => $totalPrice,
                'currency' => 'Fcfa',
                'subscription_status' => $dossier->hasActiveSubscription() ? 'active' : 'inactive',
                'subscription_end' => $dossier->getSubscriptionExpirationDate(),
                'libelle' => $mainFrais ? $mainFrais->libelle : null,
            ],
        ], 200);
    }
}
