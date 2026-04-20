<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use App\Models\Frais;
use App\Models\Paiement;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    public function onlineHistory()
    {
        $medicalOnlinePayments = Paiement::query()
            ->with(['dossierMedical.user'])
            ->where('notes', 'like', '%self-service%')
            ->latest('date_encaissement')
            ->paginate(20, ['*'], 'medical_page');

        $professionalOnlinePayments = DossierProfessionnel::query()
            ->with('user', 'frais')
            ->where('statut_paiement_inscription', 'paye')
            ->whereNotNull('reference_paiement_inscription')
            ->where('notes', 'like', '%self-service%')
            ->latest('encaisse_le')
            ->paginate(20, ['*'], 'pro_page');

        return view('paiements.online-history', compact('medicalOnlinePayments', 'professionalOnlinePayments'));
    }

    /**
     * Display a listing of paiements for a specific dossier.
     */
    public function index(Request $request)
    {
        $dossierId = $request->get('dossier_id');

        $query = Paiement::with(['dossierMedical', 'encaissePar']);

        if ($dossierId) {
            $query->where('dossier_medical_id', $dossierId);
        }

        $paiements = $query->latest()->paginate(15);

        return view('paiements.index', compact('paiements', 'dossierId'));
    }

    /**
     * Show the form for creating a new paiement.
     */
    public function create(Request $request)
    {
        $dossierId = $request->get('dossier_id');
        $dossier = DossierMedical::findOrFail($dossierId);

        // Calculer la prochaine période à payer
        $dernierPaiement = $dossier->paiements()->payes()->latest()->first();

        if ($dernierPaiement) {
            $prochainePeriodeDebut = Carbon::parse($dernierPaiement->periode_fin)->addDay();
        } else {
            // Premier paiement - commence ce mois-ci
            $prochainePeriodeDebut = Carbon::now()->startOfMonth();
        }

        return view('paiements.create', compact('dossier', 'prochainePeriodeDebut'));
    }

    /**
     * Store a newly created paiement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_medical_id' => 'required|exists:dossiers_medicaux,id',
            'frais_inscription_id' => 'nullable|exists:frais_inscriptions,id',
            'type_paiement' => 'required|in:inscription,reabonnement',
            'montant' => 'required|numeric|min:0',
            'nombre_mois' => 'required|integer|min:1|max:12',
            'periode_debut' => 'required|date',
            'mode_paiement' => 'required|in:cash,en_ligne,mobile_money,carte,virement',
            'reference_paiement' => 'nullable|string|unique:paiements',
            'notes' => 'nullable|string',
        ]);

        $periodeDebut = Carbon::parse($validated['periode_debut']);
        $periodeFin = $periodeDebut->copy()->addMonths($validated['nombre_mois'])->subDay();

        DB::transaction(function () use ($validated, $periodeDebut, $periodeFin) {
            Paiement::create([
                'dossier_medical_id' => $validated['dossier_medical_id'],
                'frais_inscription_id' => $validated['frais_inscription_id'],
                'type_paiement' => $validated['type_paiement'],
                'montant' => $validated['montant'],
                'periode_debut' => $periodeDebut,
                'periode_fin' => $periodeFin,
                'nombre_mois' => $validated['nombre_mois'],
                'statut' => 'paye', // Marquer comme payé immédiatement
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes'],
                'encaisse_par_user_id' => auth()->id(),
                'date_encaissement' => now(),
            ]);

            // Mettre à jour le statut du dossier médical
            $this->updateDossierStatus($validated['dossier_medical_id']);
        });

        return redirect()
            ->route('dossier-medicals.show', $validated['dossier_medical_id'])
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Display the specified paiement.
     */
    public function show(Paiement $paiement)
    {
        $paiement->load(['dossierMedical', 'encaissePar']);

        return view('paiements.show', compact('paiement'));
    }

    /**
     * Show the form for editing a paiement.
     */
    public function edit(Paiement $paiement)
    {
        return view('paiements.edit', compact('paiement'));
    }

    /**
     * Update the specified paiement.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'frais_inscription_id' => 'nullable|exists:frais_inscriptions,id',
            'type_paiement' => 'required|in:inscription,reabonnement',
            'montant' => 'required|numeric|min:0',
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date',
            'nombre_mois' => 'required|integer|min:1|max:12',
            'statut' => 'required|in:en_attente,paye,annule,rembourse',
            'mode_paiement' => 'required|in:cash,en_ligne,mobile_money,carte,virement',
            'reference_paiement' => 'nullable|string|unique:paiements,reference_paiement,'.$paiement->id,
            'date_encaissement' => 'nullable|datetime',
            'notes' => 'nullable|string',
        ]);

        $ancienStatut = $paiement->statut;

        $paiement->update($validated);

        // Si le statut a changé, mettre à jour le statut du dossier
        if ($ancienStatut !== $validated['statut']) {
            $this->updateDossierStatus($paiement->dossier_medical_id);
        }

        return redirect()
            ->route('paiements.show', $paiement)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Remove the specified paiement.
     */
    public function destroy(Paiement $paiement)
    {
        $dossierId = $paiement->dossier_medical_id;

        $paiement->delete();

        // Mettre à jour le statut du dossier après suppression
        $this->updateDossierStatus($dossierId);

        return redirect()
            ->route('dossier-medicals.show', $dossierId)
            ->with('success', 'Paiement supprimé avec succès.');
    }

    /**
     * Update the status of a dossier medical based on its paiements.
     */
    private function updateDossierStatus($dossierId)
    {
        $dossier = DossierMedical::find($dossierId);

        if (! $dossier) {
            return;
        }

        // Vérifier s'il y a des paiements actifs
        $hasActivePaiement = $dossier->hasActivePaiement();

        // Mettre à jour le statut actif du dossier
        $dossier->update(['actif' => $hasActivePaiement]);
    }

    /**
     * Generate a PDF receipt for a paiement.
     */
    public function pdf(Paiement $paiement)
    {
        $paiement->load(['dossierMedical', 'fraisInscription', 'encaissePar']);

        return view('paiements.pdf', compact('paiement'));
    }

    /**
     * Confirm a pending paiement (change status from en_attente to paye).
     * Auto-creates the first subscription when registration payment is confirmed.
     */
    public function confirmPayment(Paiement $paiement)
    {
        if ($paiement->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Ce paiement ne peut pas être confirmé.');
        }

        DB::transaction(function () use ($paiement) {
            $paiement->update([
                'statut' => 'paye',
                'encaisse_par_user_id' => auth()->id(),
                'date_encaissement' => now(),
            ]);

            // Mettre à jour le statut du dossier médical
            $this->updateDossierStatus($paiement->dossier_medical_id);

            // Si c'est un paiement d'inscription, créer automatiquement la première subscription
            if ($paiement->type_paiement === 'inscription') {
                $this->createFirstSubscription($paiement);
            }
        });

        return redirect()->back()->with('success', 'Paiement confirmé avec succès et abonnement créé.');
    }

    /**
     * Create the first subscription after registration payment is confirmed.
     */
    private function createFirstSubscription(Paiement $paiement)
    {
        // Chercher un frais de type réabonnement pour la première subscription
        $fraisReabonnement = Frais::where('type', 'reabonnement')->first();

        // Si pas de frais de réabonnement trouvé, utiliser le frais d'inscription
        if (! $fraisReabonnement) {
            $fraisReabonnement = Frais::find($paiement->frais_inscription_id);
        }

        if (! $fraisReabonnement) {
            return; // Pas de frais disponible, on ne crée pas de subscription
        }

        // Vérifier si une subscription existe déjà pour ce dossier
        $existingSubscription = Subscription::where('dossier_medical_id', $paiement->dossier_medical_id)->exists();

        if ($existingSubscription) {
            return; // Une subscription existe déjà
        }

        // Créer la première subscription (1 mois gratuit avec l'inscription)
        Subscription::create([
            'dossier_medical_id' => $paiement->dossier_medical_id,
            'frais_id' => $fraisReabonnement->id,
            'date_debut' => now(),
            'date_fin' => now()->addMonth()->subDay(),
            'nombre_mois' => 1,
            'montant' => 0, // Premier mois gratuit avec l'inscription
            'statut' => 'actif',
            'mode_paiement' => $paiement->mode_paiement,
            'encaisse_par_user_id' => auth()->id(),
            'date_paiement' => now(),
            'notes' => 'Premier mois d\'abonnement inclus avec l\'inscription',
        ]);
    }

    /**
     * Check and update expired paiements (could be called by a scheduled job).
     */
    public function checkExpiredPaiements()
    {
        $expiredPaiements = Paiement::echus()->get();

        foreach ($expiredPaiements as $paiement) {
            // Mettre à jour le statut du dossier si nécessaire
            $this->updateDossierStatus($paiement->dossier_medical_id);
        }

        return response()->json([
            'message' => 'Vérification des paiements expirés terminée',
            'expired_count' => $expiredPaiements->count(),
        ]);
    }
}
