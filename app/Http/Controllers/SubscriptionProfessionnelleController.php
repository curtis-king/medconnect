<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionProfessionnelleRequest;
use App\Models\DossierProfessionnel;
use App\Models\Frais;
use App\Models\SubscriptionProfessionnelle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubscriptionProfessionnelleController extends Controller
{
    public function index(DossierProfessionnel $dossierProfessionnel): View
    {
        SubscriptionProfessionnelle::updateExpiredStatuses();

        $subscriptions = $dossierProfessionnel->subscriptions()
            ->with('frais', 'encaissePar')
            ->latest()
            ->paginate(15);

        $fraisReabonnement = Frais::where('type', 'reabonnement_pro')->get();

        return view('subscriptions-professionnelles.index', compact('dossierProfessionnel', 'subscriptions', 'fraisReabonnement'));
    }

    public function create(DossierProfessionnel $dossierProfessionnel): View
    {
        $fraisReabonnement = Frais::where('type', 'reabonnement_pro')->get();

        return view('subscriptions-professionnelles.create', compact('dossierProfessionnel', 'fraisReabonnement'));
    }

    public function store(StoreSubscriptionProfessionnelleRequest $request, DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        return DB::transaction(function () use ($request, $dossierProfessionnel) {
            SubscriptionProfessionnelle::createWithAutoDate(
                $dossierProfessionnel->id,
                $request->frais_id,
                $request->nombre_mois,
                null,
                $request->mode_paiement,
                Auth::id()
            );

            if ($request->filled('reference_paiement')) {
                $dossierProfessionnel->subscriptions()->latest()->first()
                    ?->update(['reference_paiement' => $request->reference_paiement]);
            }

            return redirect()->route('subscriptions-pro.index', $dossierProfessionnel)
                ->with('success', 'Abonnement créé avec succès.');
        });
    }

    public function show(DossierProfessionnel $dossierProfessionnel, SubscriptionProfessionnelle $subscription): View
    {
        return view('subscriptions-professionnelles.show', compact('dossierProfessionnel', 'subscription'));
    }

    public function cancel(DossierProfessionnel $dossierProfessionnel, SubscriptionProfessionnelle $subscription): RedirectResponse
    {
        $subscription->update(['statut' => 'annule']);

        return redirect()->route('subscriptions-pro.index', $dossierProfessionnel)
            ->with('success', 'Abonnement annulé.');
    }

    public function calculer(Request $request, DossierProfessionnel $dossierProfessionnel): JsonResponse
    {
        $request->validate([
            'frais_id' => ['required', 'exists:frais,id'],
            'nombre_mois' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $frais = Frais::findOrFail($request->frais_id);
        $nombreMois = (int) $request->nombre_mois;

        $lastSubscription = $dossierProfessionnel->subscriptions()
            ->where('statut', '!=', 'annule')
            ->orderBy('date_fin', 'desc')
            ->first();

        if ($lastSubscription && $lastSubscription->date_fin >= now()->toDateString()) {
            $dateDebut = $lastSubscription->date_fin->copy()->addDay();
        } else {
            $dateDebut = now();
        }

        $dateFin = SubscriptionProfessionnelle::calculateEndDate($dateDebut, $nombreMois);
        $montant = $frais->prix * $nombreMois;

        return response()->json([
            'date_debut' => $dateDebut->format('d/m/Y'),
            'date_fin' => $dateFin->format('d/m/Y'),
            'montant' => $montant,
            'montant_formatted' => number_format($montant, 0, ',', ' ').' XAF',
        ]);
    }

    public function getFraisReabonnement(): JsonResponse
    {
        $frais = Frais::where('type', 'reabonnement_pro')->get();

        return response()->json($frais);
    }
}
