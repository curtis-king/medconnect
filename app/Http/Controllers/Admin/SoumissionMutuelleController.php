<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSoumissionMutuelleRequest;
use App\Models\FactureProfessionnelle;
use App\Models\SoumissionMutuelle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SoumissionMutuelleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'statut' => ['nullable', 'in:soumis,en_traitement,approuve,rejete,partiel'],
            'backoffice_status' => ['nullable', 'in:en_attente,valide,rejete,paye'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = SoumissionMutuelle::query()
            ->with([
                'dossierMedical.user',
                'factureProfessionnelle.dossierProfessionnel.user',
                'subscription',
            ])
            ->latest('date_soumission');

        if (filled($filters['statut'] ?? null)) {
            $query->where('statut', $filters['statut']);
        }

        if (filled($filters['backoffice_status'] ?? null)) {
            $query->whereHas('factureProfessionnelle', function ($builder) use ($filters): void {
                $builder->where('statut_backoffice', $filters['backoffice_status']);
            });
        }

        if (filled($filters['q'] ?? null)) {
            $search = trim((string) $filters['q']);

            $query->where(function ($builder) use ($search): void {
                $builder->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('factureProfessionnelle', function ($factureQuery) use ($search): void {
                        $factureQuery->where('reference', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dossierMedical.user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $statsQuery = SoumissionMutuelle::query();

        return view('admin.soumissions-mutuelle.index', [
            'soumissions' => $query->paginate(15)->withQueryString(),
            'filters' => $filters,
            'stats' => [
                'submitted_count' => (clone $statsQuery)->where('statut', 'soumis')->count(),
                'processing_count' => (clone $statsQuery)->where('statut', 'en_traitement')->count(),
                'approved_waiting_payment_count' => (clone $statsQuery)
                    ->whereIn('statut', ['approuve', 'partiel'])
                    ->whereHas('factureProfessionnelle', function ($builder): void {
                        $builder->where('statut_backoffice', 'valide');
                    })
                    ->count(),
                'paid_count' => (clone $statsQuery)
                    ->whereHas('factureProfessionnelle', function ($builder): void {
                        $builder->where('statut_backoffice', 'paye');
                    })
                    ->count(),
                'rejected_count' => (clone $statsQuery)->where('statut', 'rejete')->count(),
            ],
        ]);
    }

    public function show(SoumissionMutuelle $soumissionMutuelle): View
    {
        $soumissionMutuelle->load([
            'dossierMedical.user',
            'factureProfessionnelle.dossierProfessionnel.user',
            'factureProfessionnelle.serviceProfessionnel',
            'subscription',
        ]);

        return view('admin.soumissions-mutuelle.show', [
            'soumission' => $soumissionMutuelle,
        ]);
    }

    public function update(UpdateSoumissionMutuelleRequest $request, SoumissionMutuelle $soumissionMutuelle): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($soumissionMutuelle, $payload): void {
            /** @var FactureProfessionnelle $facture */
            $facture = $soumissionMutuelle->factureProfessionnelle()->lockForUpdate()->firstOrFail();
            $action = $payload['action'];
            $notes = $payload['notes'] ?? $soumissionMutuelle->notes;

            if ($action === 'processing') {
                $soumissionMutuelle->update([
                    'statut' => 'en_traitement',
                    'notes' => $notes,
                ]);

                $facture->update([
                    'statut_backoffice' => 'en_attente',
                ]);

                return;
            }

            if ($action === 'reject') {
                $soumissionMutuelle->update([
                    'statut' => 'rejete',
                    'montant_pris_en_charge' => 0,
                    'montant_rejete' => (float) $soumissionMutuelle->montant_soumis,
                    'date_traitement' => now(),
                    'motif_rejet' => $payload['motif_rejet'],
                    'notes' => $notes,
                ]);

                $facture->update([
                    'montant_couvert_mutuelle' => 0,
                    'montant_a_charge_patient' => (float) $facture->montant_total,
                    'statut_mutuelle' => 'rejete',
                    'statut_backoffice' => 'rejete',
                    'prise_en_charge_confirmee_le' => null,
                ]);

                return;
            }

            if ($action === 'approve') {
                $coveredAmount = (float) $payload['montant_pris_en_charge'];
                $rejectedAmount = max((float) $soumissionMutuelle->montant_soumis - $coveredAmount, 0);
                $mutuelleStatus = $rejectedAmount > 0 ? 'partiel' : 'approuve';
                $remainingPatientAmount = max((float) $facture->montant_total - $coveredAmount, 0);

                $soumissionMutuelle->update([
                    'statut' => $mutuelleStatus,
                    'montant_pris_en_charge' => $coveredAmount,
                    'montant_rejete' => $rejectedAmount,
                    'date_traitement' => now(),
                    'motif_rejet' => $rejectedAmount > 0 ? ($payload['motif_rejet'] ?? 'Partie hors plafond ou non couverte.') : null,
                    'notes' => $notes,
                ]);

                $facture->update([
                    'montant_couvert_mutuelle' => $coveredAmount,
                    'montant_a_charge_patient' => $remainingPatientAmount,
                    'statut_mutuelle' => $mutuelleStatus,
                    'statut_backoffice' => 'valide',
                    'prise_en_charge_confirmee_le' => now(),
                ]);

                return;
            }

            $facture->update([
                'statut_backoffice' => 'paye',
                'statut_paiement_patient' => 'paye',
                'mode_paiement' => 'virement',
                'statut' => 'payee',
                'payee_le' => now(),
            ]);

            $soumissionMutuelle->update([
                'notes' => $notes,
                'date_traitement' => $soumissionMutuelle->date_traitement ?? now(),
            ]);
        });

        $message = match ($payload['action']) {
            'processing' => 'La demande a ete marquee en traitement.',
            'approve' => 'La demande de prise en charge a ete validee.',
            'reject' => 'La demande de prise en charge a ete rejetee.',
            'pay' => 'Le paiement backoffice a ete marque comme effectue.',
            default => 'Action appliquee avec succes.',
        };

        return back()->with('success', $message);
    }
}
