<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\Frais;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Affiche le formulaire de création de subscription (réabonnement).
     */
    public function create()
    {
        $fraisReabonnement = Frais::where('type', 'reabonnement')->get();

        return view('subscriptions.create', compact('fraisReabonnement'));
    }

    /**
     * Affiche la liste des subscriptions d'un client.
     */
    public function index(DossierMedical $dossierMedical)
    {
        $subscriptions = $dossierMedical->subscriptions()
            ->with(['frais', 'encaissePar'])
            ->orderBy('date_fin', 'desc')
            ->get();

        return view('subscriptions.index', compact('dossierMedical', 'subscriptions'));
    }

    /**
     * Recherche de dossiers pour le réabonnement.
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
                $q->orderBy('date_fin', 'desc')->limit(1);
            }])
            ->limit(10)
            ->get()
            ->map(function ($dossier) {
                $activeSubscription = $dossier->subscriptions
                    ->where('statut', 'actif')
                    ->where('date_fin', '>=', now()->toDateString())
                    ->first();

                $derniereSubscription = $dossier->subscriptions->first();

                return [
                    'id' => $dossier->id,
                    'numero_unique' => $dossier->numero_unique,
                    'nom' => $dossier->nom,
                    'prenom' => $dossier->prenom,
                    'nom_complet' => $dossier->nom_complet,
                    'telephone' => $dossier->telephone,
                    'photo_profil_path' => $dossier->photo_profil_path,
                    'est_actif' => $activeSubscription !== null,
                    'date_fin_abonnement' => $derniereSubscription?->date_fin?->format('d/m/Y'),
                    'jours_restants' => $activeSubscription
                        ? (int) now()->diffInDays($activeSubscription->date_fin, false)
                        : 0,
                ];
            });

        return response()->json(['dossiers' => $dossiers]);
    }

    /**
     * Stocke une nouvelle subscription (paiement d'abonnement).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_medical_id' => 'required|exists:dossiers_medicaux,id',
            'frais_id' => 'required|exists:frais,id',
            'nombre_mois' => 'required|integer|min:1|max:12',
            'mode_paiement' => 'required|in:especes,mobile_money,carte_bancaire,virement',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $subscription = Subscription::createWithAutoDate(
                dossierMedicalId: $validated['dossier_medical_id'],
                fraisId: $validated['frais_id'],
                nombreMois: $validated['nombre_mois'],
                modePaiement: $validated['mode_paiement'],
                encaisseParUserId: Auth::id()
            );

            if (! empty($validated['reference_paiement'])) {
                $subscription->update(['reference_paiement' => $validated['reference_paiement']]);
            }

            if (! empty($validated['notes'])) {
                $subscription->update(['notes' => $validated['notes']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Abonnement créé avec succès pour '.$subscription->nombre_mois.' mois.',
                'subscription' => $subscription->load(['frais', 'dossierMedical']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'abonnement: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Affiche les détails d'une subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['dossierMedical', 'frais', 'encaissePar']);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Annule une subscription.
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->statut === 'annule') {
            return response()->json([
                'success' => false,
                'message' => 'Cette subscription est déjà annulée.',
            ], 400);
        }

        $subscription->update(['statut' => 'annule']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription annulée avec succès.',
        ]);
    }

    /**
     * Récupère les frais de réabonnement disponibles.
     */
    public function getFraisReabonnement()
    {
        $frais = Frais::where('type', 'reabonnement')->get();

        return response()->json($frais);
    }

    /**
     * Calcule le montant et les dates pour un abonnement.
     */
    public function calculer(Request $request)
    {
        $validated = $request->validate([
            'dossier_medical_id' => 'required|exists:dossiers_medicaux,id',
            'frais_id' => 'required|exists:frais,id',
            'nombre_mois' => 'required|integer|min:1|max:12',
        ]);

        $frais = Frais::findOrFail($validated['frais_id']);
        $montant = $frais->prix * $validated['nombre_mois'];

        // Déterminer la date de début
        $lastSubscription = Subscription::where('dossier_medical_id', $validated['dossier_medical_id'])
            ->where('statut', '!=', 'annule')
            ->orderBy('date_fin', 'desc')
            ->first();

        if ($lastSubscription && $lastSubscription->date_fin >= now()->toDateString()) {
            $dateDebut = $lastSubscription->date_fin->copy()->addDay();
        } else {
            $dateDebut = now();
        }

        $dateFin = Subscription::calculateEndDate($dateDebut, $validated['nombre_mois']);

        return response()->json([
            'montant' => $montant,
            'montant_formatted' => number_format($montant, 0, ',', ' ').' XAF',
            'date_debut' => $dateDebut->format('Y-m-d'),
            'date_debut_formatted' => $dateDebut->format('d/m/Y'),
            'date_fin' => $dateFin->format('Y-m-d'),
            'date_fin_formatted' => $dateFin->format('d/m/Y'),
            'frais_unitaire' => $frais->prix,
            'frais_unitaire_formatted' => number_format($frais->prix, 0, ',', ' ').' XAF',
        ]);
    }
}
