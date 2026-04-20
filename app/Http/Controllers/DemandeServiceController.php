<?php

namespace App\Http\Controllers;

use App\Models\DemandeDecision;
use App\Models\DemandeFacture;
use App\Models\DemandePieceJointe;
use App\Models\DemandeRendezVous;
use App\Models\DemandeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeServiceController extends Controller
{
    public function index()
    {
        $demandes = DemandeService::with(['user', 'service', 'traitePar'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('demandes-services.index', compact('demandes'));
    }

    public function enAttente()
    {
        $demandes = DemandeService::with(['user', 'service'])
            ->where('statut', 'en_attente')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('demandes-services.en-attente', compact('demandes'));
    }

    public function show(DemandeService $demande)
    {
        $demande->load(['user', 'service', 'traitePar', 'dossier']);

        return view('demandes-services.show', compact('demande'));
    }

    public function edit(DemandeService $demande)
    {
        $demande->load(['user', 'service', 'traitePar', 'dossier', 'piecesJointes', 'decisions', 'rendezVous', 'factures']);

        $user = $demande->user;
        $dossier = $demande->dossier;

        $adresseComplete = collect([
            $dossier?->adresse ?? $user->address,
            $user->quartier,
            $user->city,
        ])->filter()->implode(', ');

        $hasLocation = $user->latitude !== null && $user->longitude !== null;
        $mapUrl = null;
        $gpsUrl = null;

        if ($hasLocation) {
            $mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox='.($user->longitude - 0.01).'%2C'.($user->latitude - 0.01).'%2C'.($user->longitude + 0.01).'%2C'.($user->latitude + 0.01).'&layer=mapnik&marker='.$user->latitude.'%2C'.$user->longitude;
            $gpsUrl = 'https://www.google.com/maps/dir/?api=1&destination='.$user->latitude.','.$user->longitude;
        }

        return view('demandes-services.edit', compact('demande', 'adresseComplete', 'hasLocation', 'mapUrl', 'gpsUrl'));
    }

    public function valider(Request $request, DemandeService $demande)
    {
        $demande->update([
            'statut' => 'valide',
            'traite_par_user_id' => Auth::id(),
            'traite_le' => now(),
            'reponse_backoffice' => $request->reponse ?? 'Demande validée',
        ]);

        DemandeDecision::create([
            'demande_service_id' => $demande->id,
            'type' => 'validation',
            'taken_by_user_id' => Auth::id(),
            'taken_at' => now(),
        ]);

        return redirect()->route('demandes-services.edit', $demande)
            ->with('success', 'Demande validée avec succès');
    }

    public function rejeter(Request $request, DemandeService $demande)
    {
        $request->validate(['reponse' => 'required|string']);

        $demande->update([
            'statut' => 'rejete',
            'traite_par_user_id' => Auth::id(),
            'traite_le' => now(),
            'reponse_backoffice' => $request->reponse,
        ]);

        DemandeDecision::create([
            'demande_service_id' => $demande->id,
            'type' => 'rejet',
            'motif' => $request->reponse,
            'taken_by_user_id' => Auth::id(),
            'taken_at' => now(),
        ]);

        return redirect()->route('demandes-services.edit', $demande)
            ->with('error', 'Demande rejetée');
    }

    public function terminer(Request $request, DemandeService $demande)
    {
        $demande->update([
            'statut' => 'termine',
            'traite_par_user_id' => Auth::id(),
            'traite_le' => now(),
            'reponse_backoffice' => $request->reponse ?? 'Demande traitée',
        ]);

        DemandeDecision::create([
            'demande_service_id' => $demande->id,
            'type' => 'terminer',
            'taken_by_user_id' => Auth::id(),
            'taken_at' => now(),
        ]);

        return redirect()->route('demandes-services.index')
            ->with('success', 'Demande marquée comme terminée');
    }

    public function storePieceJointe(Request $request, DemandeService $demande)
    {
        $request->validate([
            'type' => 'required|string',
            'fichier' => 'required|file|max:10240',
        ]);

        $path = $request->file('fichier')->store('demandes/pieces-jointes', 'public');

        DemandePieceJointe::create([
            'demande_service_id' => $demande->id,
            'type' => $request->type,
            'nom_fichier' => $request->file('fichier')->getClientOriginalName(),
            'chemin_fichier' => $path,
            'mime_type' => $request->file('fichier')->getMimeType(),
            'taille_fichier' => $request->file('fichier')->getSize(),
            'uploaded_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('demandes-services.edit', $demande)
            ->with('success', 'Pièce jointe ajoutée');
    }

    public function storeRendezVous(Request $request, DemandeService $demande)
    {
        $request->validate([
            'date_rendez_vous' => 'required|date',
            'lieu' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DemandeRendezVous::create([
            'demande_service_id' => $demande->id,
            'date_rendez_vous' => $request->date_rendez_vous,
            'lieu' => $request->lieu,
            'adresse' => $request->adresse,
            'notes' => $request->notes,
            'status' => 'planifie',
        ]);

        $demande->update(['statut' => 'valide']);

        return redirect()->route('demandes-services.edit', $demande)
            ->with('success', 'Rendez-vous planifié');
    }

    public function storeFacture(Request $request, DemandeService $demande)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_echeance' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        DemandeFacture::create([
            'demande_service_id' => $demande->id,
            'numero_facture' => DemandeFacture::generateNumero(),
            'montant' => $request->montant,
            'date_echeance' => $request->date_echeance,
            'notes' => $request->notes,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('demandes-services.edit', $demande)
            ->with('success', 'Facture créée');
    }
}
