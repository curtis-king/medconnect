<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\Frais;
use App\Services\IdentityComplianceReviewService;
use Illuminate\Http\Request;

class ControleClientController extends Controller
{
    /**
     * Display the search/control page.
     */
    public function index()
    {
        // Récupérer les frais de réabonnement pour le formulaire
        $fraisReabonnement = Frais::where('type', 'reabonnement')->get();

        return view('controle-client.index', compact('fraisReabonnement'));
    }

    /**
     * Search for a dossier by numero, nom, or prenom.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json(['dossiers' => []]);
        }

        $dossiers = DossierMedical::query()
            ->where(function ($q) use ($query) {
                $q->where('numero_unique', 'LIKE', "%{$query}%")
                    ->orWhere('nom', 'LIKE', "%{$query}%")
                    ->orWhere('prenom', 'LIKE', "%{$query}%")
                    ->orWhereRaw("CONCAT(prenom, ' ', nom) LIKE ?", ["%{$query}%"])
                    ->orWhereRaw("CONCAT(nom, ' ', prenom) LIKE ?", ["%{$query}%"]);
            })
            ->with(['subscriptions' => function ($q) {
                $q->orderBy('date_fin', 'desc')->limit(5);
            }])
            ->limit(10)
            ->get()
            ->map(function ($dossier) {
                $activeSubscription = $dossier->subscriptions
                    ->where('statut', 'actif')
                    ->where('date_fin', '>=', now()->toDateString())
                    ->first();

                $derniereSubscription = $dossier->subscriptions->first();
                $estActif = $activeSubscription !== null;

                return [
                    'id' => $dossier->id,
                    'numero_unique' => $dossier->numero_unique,
                    'nom' => $dossier->nom,
                    'prenom' => $dossier->prenom,
                    'nom_complet' => $dossier->nom_complet,
                    'telephone' => $dossier->telephone,
                    'date_naissance' => $dossier->date_naissance?->format('d/m/Y'),
                    'photo_profil_path' => $dossier->photo_profil_path,
                    'est_actif' => $estActif,
                    'subscription_active' => $activeSubscription ? [
                        'id' => $activeSubscription->id,
                        'date_debut' => $activeSubscription->date_debut->format('d/m/Y'),
                        'date_fin' => $activeSubscription->date_fin->format('d/m/Y'),
                        'montant' => number_format($activeSubscription->montant, 0, ',', ' ').' XAF',
                        'jours_restants' => (int) now()->diffInDays($activeSubscription->date_fin, false),
                    ] : null,
                    'derniere_subscription' => $derniereSubscription ? [
                        'id' => $derniereSubscription->id,
                        'statut' => $derniereSubscription->statut,
                        'date_debut' => $derniereSubscription->date_debut->format('d/m/Y'),
                        'date_fin' => $derniereSubscription->date_fin->format('d/m/Y'),
                        'montant' => number_format($derniereSubscription->montant, 0, ',', ' ').' XAF',
                        'est_expire' => $derniereSubscription->date_fin < now()->toDateString(),
                    ] : null,
                ];
            });

        return response()->json(['dossiers' => $dossiers]);
    }

    /**
     * Get details of a specific dossier for control.
     */
    public function details($id, IdentityComplianceReviewService $identityComplianceReviewService)
    {
        $dossier = DossierMedical::with([
            'subscriptions' => function ($q) {
                $q->orderBy('date_fin', 'desc');
            },
            'subscriptions.frais',
            'subscriptions.encaissePar',
            'paiements' => function ($q) {
                $q->orderBy('created_at', 'desc')->limit(5);
            },
        ])->findOrFail($id);

        $activeSubscription = $dossier->subscriptions
            ->where('statut', 'actif')
            ->where('date_fin', '>=', now()->toDateString())
            ->first();

        $derniereSubscription = $dossier->subscriptions->first();
        $estActif = $activeSubscription !== null;

        // Calculer la prochaine date de début pour un nouvel abonnement
        if ($derniereSubscription && $derniereSubscription->date_fin >= now()->toDateString()) {
            $prochaineDateDebut = $derniereSubscription->date_fin->copy()->addDay();
        } else {
            $prochaineDateDebut = now();
        }

        // Récupérer les frais de réabonnement
        $fraisReabonnement = Frais::where('type', 'reabonnement')->get();

        $complianceReview = $identityComplianceReviewService->reviewMedicalDossier($dossier);

        return response()->json([
            'dossier' => [
                'id' => $dossier->id,
                'numero_unique' => $dossier->numero_unique,
                'nom' => $dossier->nom,
                'prenom' => $dossier->prenom,
                'nom_complet' => $dossier->nom_complet,
                'telephone' => $dossier->telephone,
                'date_naissance' => $dossier->date_naissance?->format('d/m/Y'),
                'sexe' => $dossier->sexe,
                'adresse' => $dossier->adresse,
                'photo_profil_path' => $dossier->photo_profil_path,
                'documents' => [
                    'type_piece_identite' => $dossier->type_piece_identite,
                    'numero_piece_identite' => $dossier->numero_piece_identite,
                    'piece_identite_recto_path' => $dossier->piece_identite_recto_path,
                    'piece_identite_verso_path' => $dossier->piece_identite_verso_path,
                    'date_expiration_piece_identite' => $dossier->date_expiration_piece_identite?->format('d/m/Y'),
                ],
            ],
            'documents_validation' => [
                'statut' => $dossier->documents_validation_statut,
                'ia_risk_level' => $complianceReview['risk_level'] ?? $dossier->documents_validation_ia_risk_level,
                'ia_score' => $complianceReview['score'] ?? $dossier->documents_validation_ia_score,
                'ia_reasons' => $complianceReview['reasons'] ?? $dossier->documents_validation_ia_reasons,
                'source' => $complianceReview['source'] ?? 'local',
                'personnel_user_id' => $dossier->documents_validation_personnel_user_id,
                'personnel_note' => $dossier->documents_validation_personnel_note,
                'personnel_validated_at' => $dossier->documents_validation_personnel_at?->format('d/m/Y H:i'),
            ],
            'est_actif' => $estActif,
            'subscription_active' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'date_debut' => $activeSubscription->date_debut->format('d/m/Y'),
                'date_fin' => $activeSubscription->date_fin->format('d/m/Y'),
                'jours_restants' => (int) now()->diffInDays($activeSubscription->date_fin, false),
                'montant' => number_format($activeSubscription->montant, 0, ',', ' ').' XAF',
                'nombre_mois' => $activeSubscription->nombre_mois,
            ] : null,
            'prochaine_date_debut' => $prochaineDateDebut->format('d/m/Y'),
            'prochaine_date_debut_raw' => $prochaineDateDebut->format('Y-m-d'),
            'historique_subscriptions' => $dossier->subscriptions->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'date_debut' => $sub->date_debut->format('d/m/Y'),
                    'date_fin' => $sub->date_fin->format('d/m/Y'),
                    'nombre_mois' => $sub->nombre_mois,
                    'montant' => number_format($sub->montant, 0, ',', ' ').' XAF',
                    'statut' => $sub->statut,
                    'statut_label' => $sub->statut_label,
                    'mode_paiement' => $sub->mode_paiement,
                    'date_paiement' => $sub->date_paiement?->format('d/m/Y H:i'),
                    'encaisse_par' => $sub->encaissePar?->name,
                ];
            }),
            'frais_reabonnement' => $fraisReabonnement->map(function ($frais) {
                return [
                    'id' => $frais->id,
                    'libelle' => $frais->libelle,
                    'prix' => $frais->prix,
                    'prix_formatted' => number_format($frais->prix, 0, ',', ' ').' XAF',
                ];
            }),
        ]);
    }
}
