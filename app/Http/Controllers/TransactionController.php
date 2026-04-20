<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Subscription;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Affiche l'historique des transactions avec filtres et tri.
     */
    public function index(Request $request)
    {
        // Paramètres de filtrage
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $type = $request->get('type', 'tous'); // tous, inscription, reabonnement
        $modePaiement = $request->get('mode_paiement', 'tous');
        $statut = $request->get('statut', 'tous'); // tous, paye, en_attente
        $encaissePar = $request->get('encaisse_par');
        $search = $request->get('search');

        // Paramètres de tri
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');

        // Mode de période
        $periode = $request->get('periode', 'jour'); // jour, semaine, mois, personnalise

        // Calculer les dates selon la période
        switch ($periode) {
            case 'semaine':
                $dateDebut = now()->startOfWeek()->format('Y-m-d');
                $dateFin = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'mois':
                $dateDebut = now()->startOfMonth()->format('Y-m-d');
                $dateFin = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'personnalise':
                $dateDebut = $dateDebut ?: now()->format('Y-m-d');
                $dateFin = $dateFin ?: now()->format('Y-m-d');
                break;
            default: // jour
                $dateDebut = $date;
                $dateFin = $date;
        }

        // Récupérer les paiements (inscriptions)
        $paiementsQuery = Paiement::query()
            ->with(['dossierMedical', 'frais', 'encaissePar'])
            ->whereDate('created_at', '>=', $dateDebut)
            ->whereDate('created_at', '<=', $dateFin);

        // Récupérer les subscriptions (réabonnements)
        $subscriptionsQuery = Subscription::query()
            ->with(['dossierMedical', 'frais', 'encaissePar'])
            ->whereDate('date_paiement', '>=', $dateDebut)
            ->whereDate('date_paiement', '<=', $dateFin)
            ->where('statut', 'paye');

        // Filtres communs
        if ($statut !== 'tous') {
            $paiementsQuery->where('statut', $statut);
        }

        if ($modePaiement !== 'tous') {
            $paiementsQuery->where('mode_paiement', $modePaiement);
            $subscriptionsQuery->where('mode_paiement', $modePaiement);
        }

        if ($encaissePar) {
            $paiementsQuery->where('encaisse_par_user_id', $encaissePar);
            $subscriptionsQuery->where('encaisse_par_user_id', $encaissePar);
        }

        if ($search) {
            $paiementsQuery->whereHas('dossierMedical', function ($q) use ($search) {
                $q->where('numero_unique', 'LIKE', "%{$search}%")
                    ->orWhere('nom', 'LIKE', "%{$search}%")
                    ->orWhere('prenom', 'LIKE', "%{$search}%");
            });
            $subscriptionsQuery->whereHas('dossierMedical', function ($q) use ($search) {
                $q->where('numero_unique', 'LIKE', "%{$search}%")
                    ->orWhere('nom', 'LIKE', "%{$search}%")
                    ->orWhere('prenom', 'LIKE', "%{$search}%");
            });
        }

        // Construire la collection unifiée
        $transactions = collect();

        // Ajouter les paiements si type = tous ou inscription
        if ($type === 'tous' || $type === 'inscription') {
            $paiements = $paiementsQuery->get()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'type' => 'inscription',
                    'type_label' => 'Inscription',
                    'date' => $p->date_encaissement ?? $p->created_at,
                    'montant' => $p->montant,
                    'mode_paiement' => $p->mode_paiement,
                    'statut' => $p->statut,
                    'reference' => $p->reference_paiement,
                    'dossier' => $p->dossierMedical,
                    'frais' => $p->frais,
                    'encaisse_par' => $p->encaissePar,
                    'notes' => $p->notes,
                    'model' => 'paiement',
                ];
            });
            $transactions = $transactions->merge($paiements);
        }

        // Ajouter les subscriptions si type = tous ou reabonnement
        if ($type === 'tous' || $type === 'reabonnement') {
            $subscriptions = $subscriptionsQuery->get()->map(function ($s) {
                return [
                    'id' => $s->id,
                    'type' => 'reabonnement',
                    'type_label' => 'Réabonnement',
                    'date' => $s->date_paiement,
                    'montant' => $s->montant,
                    'mode_paiement' => $s->mode_paiement,
                    'statut' => $s->statut,
                    'reference' => $s->reference_paiement,
                    'dossier' => $s->dossierMedical,
                    'frais' => $s->frais,
                    'encaisse_par' => $s->encaissePar,
                    'notes' => $s->notes,
                    'nombre_mois' => $s->nombre_mois,
                    'model' => 'subscription',
                ];
            });
            $transactions = $transactions->merge($subscriptions);
        }

        // Trier les transactions
        $transactions = match ($sortBy) {
            'montant' => $transactions->sortBy('montant', SORT_REGULAR, $sortOrder === 'desc'),
            'client' => $transactions->sortBy(fn ($t) => $t['dossier']->nom ?? '', SORT_REGULAR, $sortOrder === 'desc'),
            'type' => $transactions->sortBy('type', SORT_REGULAR, $sortOrder === 'desc'),
            'mode' => $transactions->sortBy('mode_paiement', SORT_REGULAR, $sortOrder === 'desc'),
            default => $transactions->sortBy('date', SORT_REGULAR, $sortOrder === 'desc'),
        };

        // Calculer les statistiques
        $stats = [
            'total' => $transactions->sum('montant'),
            'count' => $transactions->count(),
            'inscriptions' => $transactions->where('type', 'inscription')->sum('montant'),
            'inscriptions_count' => $transactions->where('type', 'inscription')->count(),
            'reabonnements' => $transactions->where('type', 'reabonnement')->sum('montant'),
            'reabonnements_count' => $transactions->where('type', 'reabonnement')->count(),
            'par_mode' => $transactions->groupBy('mode_paiement')->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('montant'),
            ]),
        ];

        // Utilisateurs pour le filtre encaissé par
        $users = \App\Models\User::whereIn('role', ['admin', 'professional'])->get();

        return view('transactions.index', compact(
            'transactions',
            'stats',
            'users',
            'date',
            'dateDebut',
            'dateFin',
            'type',
            'modePaiement',
            'statut',
            'encaissePar',
            'search',
            'sortBy',
            'sortOrder',
            'periode'
        ));
    }

    /**
     * Export des transactions en CSV.
     */
    public function export(Request $request)
    {
        // Réutiliser la logique de index pour obtenir les transactions
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateDebut = $request->get('date_debut', $date);
        $dateFin = $request->get('date_fin', $date);

        $paiements = Paiement::query()
            ->with(['dossierMedical', 'frais', 'encaissePar'])
            ->whereDate('created_at', '>=', $dateDebut)
            ->whereDate('created_at', '<=', $dateFin)
            ->get();

        $subscriptions = Subscription::query()
            ->with(['dossierMedical', 'frais', 'encaissePar'])
            ->whereDate('date_paiement', '>=', $dateDebut)
            ->whereDate('date_paiement', '<=', $dateFin)
            ->where('statut', 'paye')
            ->get();

        $filename = "transactions_{$dateDebut}_to_{$dateFin}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($paiements, $subscriptions) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Date',
                'Type',
                'Client',
                'N° Dossier',
                'Montant',
                'Mode Paiement',
                'Statut',
                'Encaissé par',
                'Référence',
            ]);

            // Paiements
            foreach ($paiements as $p) {
                fputcsv($file, [
                    $p->created_at->format('d/m/Y H:i'),
                    'Inscription',
                    $p->dossierMedical?->nom_complet ?? 'N/A',
                    $p->dossierMedical?->numero_unique ?? 'N/A',
                    number_format($p->montant, 0, ',', ' ').' XAF',
                    $p->mode_paiement,
                    $p->statut,
                    $p->encaissePar?->name ?? 'N/A',
                    $p->reference_paiement ?? '',
                ]);
            }

            // Subscriptions
            foreach ($subscriptions as $s) {
                fputcsv($file, [
                    $s->date_paiement->format('d/m/Y H:i'),
                    'Réabonnement',
                    $s->dossierMedical?->nom_complet ?? 'N/A',
                    $s->dossierMedical?->numero_unique ?? 'N/A',
                    number_format($s->montant, 0, ',', ' ').' XAF',
                    $s->mode_paiement,
                    $s->statut,
                    $s->encaissePar?->name ?? 'N/A',
                    $s->reference_paiement ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
