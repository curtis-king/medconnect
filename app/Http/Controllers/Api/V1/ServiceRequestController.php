<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\DossierMedical;
use App\Models\ServiceMedical;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function getServices(): JsonResponse
    {
        $services = ServiceMedical::where('actif', true)
            ->orderBy('type')
            ->orderBy('nom')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'nom' => $service->nom,
                    'description' => $service->description,
                    'prix' => (float) $service->prix,
                    'type' => $service->type,
                ];
            });

        return response()->json([
            'success' => true,
            'services' => $services,
        ], 200);
    }

    public function createRequest(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'service_id' => 'required|exists:service_medicals,id',
            'dossier_medical_id' => 'nullable|exists:dossiers_medicaux,id',
            'notes' => 'nullable|string',
        ]);

        $service = ServiceMedical::findOrFail($validated['service_id']);

        $dossier = null;
        if (! empty($validated['dossier_medical_id'])) {
            $dossier = DossierMedical::where('id', $validated['dossier_medical_id'])
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('responsable_user_id', $user->id);
                })
                ->first();

            if (! $dossier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dossier médical non trouvé ou non autorisé',
                ], 404);
            }
        } else {
            $dossier = DossierMedical::where('user_id', $user->id)->first();
        }

        $demande = DemandeService::create([
            'user_id' => $user->id,
            'service_medical_id' => $validated['service_id'],
            'dossier_medical_id' => $dossier?->id,
            'notes' => $validated['notes'] ?? null,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande de service soumis avec succès',
            'demande' => [
                'id' => $demande->id,
                'service' => $demande->service->nom,
                'prix' => (float) $demande->service->prix,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    public function getMyRequests(Request $request): JsonResponse
    {
        $user = $request->user();

        $demandes = DemandeService::where('user_id', $user->id)
            ->orWhereHas('dossier', function ($query) use ($user) {
                $query->where('responsable_user_id', $user->id);
            })
            ->with('service')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($demande) {
                return [
                    'id' => $demande->id,
                    'service' => $demande->service->nom,
                    'service_type' => $demande->service->type,
                    'prix' => (float) $demande->service->prix,
                    'statut' => $demande->statut,
                    'notes' => $demande->notes,
                    'reponse' => $demande->reponse_backoffice,
                    'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                    'traite_le' => $demande->traite_le?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'demandes' => $demandes,
        ], 200);
    }

    public function getDependents(Request $request): JsonResponse
    {
        $user = $request->user();

        $dependents = DossierMedical::where('responsable_user_id', $user->id)
            ->where('actif', true)
            ->get()
            ->map(function ($dossier) {
                return [
                    'id' => $dossier->id,
                    'nom' => $dossier->nom,
                    'prenom' => $dossier->prenom,
                    'numero_unique' => $dossier->numero_unique,
                ];
            });

        return response()->json([
            'success' => true,
            'dependents' => $dependents,
        ], 200);
    }

    public function getQuotaUsage(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentMonth = now()->startOfMonth();
        $endMonth = now()->endOfMonth();

        $myDemandes = DemandeService::where('user_id', $user->id)
            ->whereBetween('created_at', [$currentMonth, $endMonth])
            ->whereIn('statut', ['valide', 'termine'])
            ->with('service')
            ->get();

        $dependentDemandes = DemandeService::whereHas('dossier', function ($query) use ($user) {
            $query->where('responsable_user_id', $user->id);
        })
            ->whereBetween('created_at', [$currentMonth, $endMonth])
            ->whereIn('statut', ['valide', 'termine'])
            ->with('service')
            ->get();

        $myTotal = $myDemandes->sum(fn ($d) => (float) $d->service->prix);
        $dependentTotal = $dependentDemandes->sum(fn ($d) => (float) $d->service->prix);
        $totalUsage = $myTotal + $dependentTotal;
        $totalCount = $myDemandes->count() + $dependentDemandes->count();

        $byType = $myDemandes->concat($dependentDemandes)
            ->groupBy('service.type')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum(fn ($d) => (float) $d->service->prix),
                ];
            });

        return response()->json([
            'success' => true,
            'quota' => [
                'month' => now()->format('F Y'),
                'my_requests_count' => $myDemandes->count(),
                'my_requests_total' => $myTotal,
                'dependents_requests_count' => $dependentDemandes->count(),
                'dependents_requests_total' => $dependentTotal,
                'total_count' => $totalCount,
                'total_amount' => $totalUsage,
                'currency' => 'Fcfa',
                'by_type' => $byType,
            ],
        ], 200);
    }

    public function getDetail(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $demande = DemandeService::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('dossier', function ($q) use ($user) {
                        $q->where('responsable_user_id', $user->id);
                    });
            })
            ->with(['service', 'user', 'dossier', 'rendezVous', 'factures', 'decisions', 'piecesJointes'])
            ->first();

        if (! $demande) {
            return response()->json([
                'success' => false,
                'message' => 'Demande non trouvée',
            ], 404);
        }

        $response = [
            'id' => $demande->id,
            'statut' => $demande->statut,
            'notes' => $demande->notes,
            'reponse' => $demande->reponse_backoffice,
            'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
            'traite_le' => $demande->traite_le?->format('Y-m-d H:i:s'),
            'service' => [
                'id' => $demande->service->id,
                'nom' => $demande->service->nom,
                'type' => $demande->service->type,
                'prix' => (float) $demande->service->prix,
            ],
            'client' => [
                'nom' => $demande->user->name,
                'email' => $demande->user->email,
                'phone' => $demande->user->phone,
            ],
        ];

        if ($demande->dossier) {
            $response['dossier'] = [
                'id' => $demande->dossier->id,
                'numero' => $demande->dossier->numero_unique,
                'prenom' => $demande->dossier->prenom,
                'nom' => $demande->dossier->nom,
            ];
        }

        if ($demande->rendezVous->count() > 0) {
            $rdv = $demande->rendezVous->first();
            $response['rendez_vous'] = [
                'date' => $rdv->date_rendez_vous->format('Y-m-d H:i:s'),
                'lieu' => $rdv->lieu,
                'adresse' => $rdv->adresse,
                'status' => $rdv->status,
            ];
        }

        if ($demande->factures->count() > 0) {
            $response['factures'] = $demande->factures->map(fn ($f) => [
                'numero' => $f->numero_facture,
                'montant' => (float) $f->montant,
                'statut' => $f->statut,
                'date_echeance' => $f->date_echeance?->format('Y-m-d'),
            ])->toArray();
        }

        if ($demande->decisions->count() > 0) {
            $response['decisions'] = $demande->decisions->map(fn ($d) => [
                'type' => $d->type,
                'motif' => $d->motif,
                'date' => $d->taken_at->format('Y-m-d H:i:s'),
            ])->toArray();
        }

        if ($demande->piecesJointes->count() > 0) {
            $response['pieces_jointes'] = $demande->piecesJointes->map(fn ($p) => [
                'type' => $p->type,
                'nom' => $p->nom_fichier,
                'url' => asset('storage/'.$p->chemin_fichier),
            ])->toArray();
        }

        return response()->json([
            'success' => true,
            'demande' => $response,
        ], 200);
    }

    public function getMyRendezVous(): JsonResponse
    {
        $user = auth()->user();

        $rendezVous = \App\Models\RendezVousProfessionnel::query()
            ->where('patient_user_id', $user->id)
            ->with(['dossierProfessionnel.user', 'serviceProfessionnel'])
            ->orderBy('date_proposee')
            ->get()
            ->map(function ($rdv) {
                return [
                    'id' => $rdv->id,
                    'date' => $rdv->date_proposee->toIso8601String(),
                    'date_formatted' => $rdv->date_proposee->format('d/m/Y H:i'),
                    'service' => $rdv->serviceProfessionnel?->nom ?? 'Consultation',
                    'professionnel' => $rdv->dossierProfessionnel?->user?->name ?? 'Professionnel',
                    'statut' => $rdv->statut,
                    'mode' => $rdv->mode_deroulement ?? 'presentiel',
                    'motif' => $rdv->motif,
                ];
            });

        return response()->json([
            'success' => true,
            'rendezvous' => $rendezVous,
        ], 200);
    }
}
