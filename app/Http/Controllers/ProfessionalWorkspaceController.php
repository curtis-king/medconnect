<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConsultationProfessionnelleRequest;
use App\Http\Requests\GenerateTreatmentSuggestionRequest;
use App\Models\ConsultationDocument;
use App\Models\ConsultationProfessionnelle;
use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use App\Models\ExamenProfessionnel;
use App\Models\FactureProfessionnelle;
use App\Models\OrdonnanceProfessionnelle;
use App\Models\RendezVousProfessionnel;
use App\Models\RetraitProfessionnel;
use App\Models\ServiceProfessionnel;
use App\Notifications\ExamenAccepteNotification;
use App\Services\TreatmentSupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfessionalWorkspaceController extends Controller
{
    public function presentiel(): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $servicesConsultation = $dossierProfessionnel->services()
            ->where('actif', true)
            ->whereIn('type', ['consultation', 'autre'])
            ->orderBy('nom')
            ->get();

        $consultationsRecentes = ConsultationProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->with(['dossierMedical', 'patient', 'rendezVous', 'factures'])
            ->latest()
            ->limit(20)
            ->get();

        return view('professional.workspace-presentiel', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'servicesConsultation' => $servicesConsultation,
            'consultationsRecentes' => $consultationsRecentes,
        ]);
    }

    public function startPresentielConsultation(Request $request): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);

        $payload = $request->validate([
            'numero_dossier' => ['required', 'string', 'max:100'],
            'service_professionnel_id' => ['required', 'integer'],
            'motif' => ['nullable', 'string', 'max:1000'],
        ]);

        $numeroDossier = trim((string) $payload['numero_dossier']);

        $dossierMedical = DossierMedical::query()
            ->where('numero_unique', $numeroDossier)
            ->orWhere(fn ($query) => ctype_digit($numeroDossier)
                ? $query->whereKey((int) $numeroDossier)
                : $query->whereRaw('1 = 0'))
            ->first();

        if (! $dossierMedical) {
            return back()->withErrors([
                'numero_dossier' => 'Aucun dossier médical trouvé pour ce numéro de carte/dossier.',
            ])->withInput();
        }

        if (! $dossierMedical->actif) {
            return back()->withErrors([
                'numero_dossier' => 'Ce dossier médical est inactif.',
            ])->withInput();
        }

        if (! $dossierMedical->user_id) {
            return back()->withErrors([
                'numero_dossier' => 'Ce dossier médical n\'est pas encore lié à un compte patient.',
            ])->withInput();
        }

        if ((int) $dossierMedical->user_id === (int) $dossierProfessionnel->user_id) {
            return back()->withErrors([
                'numero_dossier' => 'Vous ne pouvez pas vous soigner vous-meme dans votre propre espace professionnel.',
            ])->withInput();
        }

        $service = $dossierProfessionnel->services()
            ->where('actif', true)
            ->whereKey((int) $payload['service_professionnel_id'])
            ->first();

        if (! $service) {
            return back()->withErrors([
                'service_professionnel_id' => 'Le service sélectionné est invalide.',
            ])->withInput();
        }

        $consultation = DB::transaction(function () use ($dossierProfessionnel, $dossierMedical, $service, $payload): ConsultationProfessionnelle {
            $now = now();

            $rendezVous = RendezVousProfessionnel::create([
                'dossier_professionnel_id' => $dossierProfessionnel->id,
                'professionnel_user_id' => $dossierProfessionnel->user_id,
                'service_professionnel_id' => $service->id,
                'patient_user_id' => $dossierMedical->user_id,
                'dossier_medical_id' => $dossierMedical->id,
                'numero_dossier_reference' => $dossierMedical->numero_unique,
                'reference' => 'RDV-PRES-'.$now->format('YmdHis').'-'.mt_rand(100, 999),
                'type_demande' => $service->type === 'examen' ? 'examen' : ($service->type === 'consultation' ? 'consultation' : 'autre'),
                'type_rendez_vous' => $service->type === 'examen' ? 'examen' : ($service->type === 'consultation' ? 'consultation' : 'autre'),
                'mode_deroulement' => 'presentiel',
                'statut' => 'accepte',
                'statut_acceptation' => 'accepte',
                'date_proposee' => $now,
                'date_proposee_jour' => $now->toDateString(),
                'heure_proposee' => $now->format('H:i:s'),
                'motif' => $payload['motif'] ?? 'Consultation présentielle au guichet',
                'notes_professionnel' => 'Patient reçu en présentiel (carte/numéro dossier saisi).',
                'decision_le' => $now,
            ]);

            $consultation = ConsultationProfessionnelle::create([
                'rendez_vous_professionnel_id' => $rendezVous->id,
                'dossier_professionnel_id' => $dossierProfessionnel->id,
                'dossier_medical_id' => $dossierMedical->id,
                'numero_dossier_reference' => $dossierMedical->numero_unique,
                'patient_user_id' => $dossierMedical->user_id,
                'type_service' => $service->type === 'examen' ? 'examen' : ($service->type === 'consultation' ? 'consultation' : 'autre'),
                'type_consultation' => 'presentiel',
                'statut' => 'brouillon',
            ]);

            FactureProfessionnelle::create([
                'rendez_vous_professionnel_id' => $rendezVous->id,
                'consultation_professionnelle_id' => $consultation->id,
                'dossier_professionnel_id' => $dossierProfessionnel->id,
                'professionnel_user_id' => $dossierProfessionnel->user_id,
                'service_professionnel_id' => $service->id,
                'patient_user_id' => $dossierMedical->user_id,
                'dossier_medical_id' => $dossierMedical->id,
                'numero_dossier_reference' => $dossierMedical->numero_unique,
                'reference' => 'FACT-PRES-'.$now->format('YmdHis').'-'.mt_rand(100, 999),
                'type_service' => $service->type,
                'type_facture' => $service->type === 'examen' ? 'examen' : ($service->type === 'consultation' ? 'consultation' : 'autre'),
                'montant_total' => (float) ($service->prix ?? 0),
                'montant_couvert_mutuelle' => 0,
                'montant_a_charge_patient' => (float) ($service->prix ?? 0),
                'statut' => 'emise',
                'statut_mutuelle' => 'non_soumis',
                'statut_backoffice' => 'en_attente',
                'envoyee_backoffice' => true,
                'soumise_backoffice_le' => $now,
                'statut_paiement_patient' => 'en_attente',
                'mode_paiement' => 'cash',
                'emise_le' => $now,
                'notes' => 'Facture physique générée depuis le parcours patient présentiel.',
            ]);

            return $consultation;
        });

        return redirect()->route('professional.workspace.consultation.edit', $consultation)
            ->with('success', 'Patient identifié. Consultation présentielle et facture physique créées.');
    }

    public function dashboard(): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $dossierProfessionnel->load(['servicesActifs']);

        $servicesActifs = $dossierProfessionnel->servicesActifs()
            ->orderBy('type')
            ->orderBy('nom')
            ->get();

        $rendezVousEnAttente = $dossierProfessionnel->rendezVous()
            ->with(['patient', 'serviceProfessionnel', 'dossierMedical'])
            ->where('statut', 'en_attente')
            ->orderBy('date_proposee')
            ->get();

        $rendezVousTraites = $dossierProfessionnel->rendezVous()
            ->with(['patient', 'serviceProfessionnel', 'facture', 'consultation'])
            ->whereIn('statut', ['accepte', 'decline', 'termine'])
            ->latest('decision_le')
            ->limit(20)
            ->get();

        $examensEnAttenteValidation = ExamenProfessionnel::query()
            ->with(['patient', 'serviceProfessionnel', 'consultation'])
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->where('statut', 'demande')
            ->whereNotNull('dossier_professionnel_recommande_id')
            ->latest()
            ->get();

        $facturesBase = FactureProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id);

        $commissionsBase = ExamenProfessionnel::query()
            ->where('dossier_professionnel_recommande_id', $dossierProfessionnel->id);

        $financeStats = [
            'encours_backoffice' => (float) (clone $facturesBase)
                ->whereIn('statut_backoffice', ['en_attente', 'valide'])
                ->sum('montant_total'),
            'factures_payees' => (float) (clone $facturesBase)
                ->where('statut_backoffice', 'paye')
                ->sum('montant_total'),
            'attente_patient' => (float) (clone $facturesBase)
                ->where('statut_paiement_patient', 'en_attente')
                ->sum('montant_a_charge_patient'),
            'commission_en_attente' => (float) (clone $commissionsBase)
                ->whereIn('statut_commission', ['en_attente', 'validee'])
                ->sum('commission_recommandation_montant'),
            'commission_payee' => (float) (clone $commissionsBase)
                ->where('statut_commission', 'payee')
                ->sum('commission_recommandation_montant'),
        ];

        return view('professional.workspace-dashboard', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'servicesActifs' => $servicesActifs,
            'rendezVousEnAttente' => $rendezVousEnAttente,
            'rendezVousTraites' => $rendezVousTraites,
            'examensEnAttenteValidation' => $examensEnAttenteValidation,
            'financeStats' => $financeStats,
            'backofficeFeedbackUnreadCount' => $this->backofficeFeedbackUnreadCount(),
        ]);
    }

    public function patientsTracking(): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $consultations = ConsultationProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->with(['dossierMedical', 'patient'])
            ->latest('updated_at')
            ->get();

        $patientsSuivis = $consultations
            ->groupBy('dossier_medical_id')
            ->map(function ($consultationsPatient) {
                $latestConsultation = $consultationsPatient->first();

                return [
                    'dossier_medical' => $latestConsultation?->dossierMedical,
                    'patient' => $latestConsultation?->patient,
                    'consultation' => $latestConsultation,
                    'total_consultations' => $consultationsPatient->count(),
                    'derniere_visite' => $latestConsultation?->updated_at,
                ];
            })
            ->sortByDesc('derniere_visite')
            ->values();

        return view('professional.workspace-patients-tracking', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'patientsSuivis' => $patientsSuivis,
            'totalConsultations' => $consultations->count(),
        ]);
    }

    public function finance(): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $factures = FactureProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->with(['dossierMedical', 'patient', 'consultation'])
            ->latest()
            ->paginate(20);

        $baseFactures = FactureProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id);

        $facturesEligiblesRetrait = $this->eligibleInvoicesForWithdrawal($dossierProfessionnel->id);
        $soldeRetirable = (float) $facturesEligiblesRetrait->sum('montant_total');

        $retraits = RetraitProfessionnel::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->withCount('factures')
            ->latest('date_demande')
            ->limit(15)
            ->get();

        $totalRetraitsTraites = (float) RetraitProfessionnel::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->whereIn('statut', ['approuve', 'paye'])
            ->get()
            ->sum(fn (RetraitProfessionnel $retrait) => (float) ($retrait->montant_approuve ?? $retrait->montant_demande));

        $facturesPayeesBrut = (float) (clone $baseFactures)
            ->where('statut_backoffice', 'paye')
            ->sum('montant_total');

        $financeStats = [
            'total_facture' => (float) (clone $baseFactures)->sum('montant_total'),
            'factures_payees' => $facturesPayeesBrut,
            'factures_payees_net' => max($facturesPayeesBrut - $totalRetraitsTraites, 0),
            'retraits_traites' => $totalRetraitsTraites,
            'encours_backoffice' => (float) (clone $baseFactures)->whereIn('statut_backoffice', ['en_attente', 'valide'])->sum('montant_total'),
            'solde_retirable' => $soldeRetirable,
        ];

        return view('professional.workspace-finance', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'factures' => $factures,
            'retraits' => $retraits,
            'financeStats' => $financeStats,
            'backofficeFeedbackUnreadCount' => $this->backofficeFeedbackUnreadCount(),
        ]);
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);

        $payload = $request->validate([
            'montant_demande' => ['required', 'numeric', 'min:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $facturesEligibles = $this->eligibleInvoicesForWithdrawal($dossierProfessionnel->id);
        $soldeRetirable = (float) $facturesEligibles->sum('montant_total');
        $montantDemande = round((float) $payload['montant_demande'], 2);

        if ($soldeRetirable <= 0) {
            return back()->with('error', 'Aucun montant n\'est actuellement retirable.');
        }

        if ($montantDemande > $soldeRetirable) {
            return back()->withErrors([
                'montant_demande' => 'Le montant demandé dépasse votre solde retirable actuel.',
            ]);
        }

        DB::transaction(function () use ($dossierProfessionnel, $facturesEligibles, $montantDemande, $payload): void {
            $now = now();

            $retrait = RetraitProfessionnel::create([
                'dossier_professionnel_id' => $dossierProfessionnel->id,
                'reference' => 'RET-'.$now->format('YmdHis').'-'.mt_rand(100, 999),
                'montant_demande' => $montantDemande,
                'statut' => 'en_attente',
                'date_demande' => $now,
                'notes' => $payload['notes'] ?? null,
            ]);

            $remainingAmount = $montantDemande;

            foreach ($facturesEligibles as $facture) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $invoiceAmount = (float) $facture->montant_total;
                $amountAttached = min($invoiceAmount, $remainingAmount);

                $retrait->factures()->attach($facture->id, [
                    'montant' => $amountAttached,
                ]);

                $remainingAmount = round($remainingAmount - $amountAttached, 2);
            }
        });

        return back()->with('success', 'Demande de retrait envoyée au backoffice avec succès.');
    }

    public function markFactureAsPaid(FactureProfessionnelle $factureProfessionnelle): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($factureProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $factureProfessionnelle->update([
            'statut' => 'payee',
            'statut_paiement_patient' => 'paye',
            'mode_paiement' => 'cash',
            'payee_le' => now(),
        ]);

        return back()->with('success', 'Facture marquée comme payée (physique).');
    }

    public function patientDirectory(): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $patients = ConsultationProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->with(['dossierMedical', 'patient'])
            ->latest('updated_at')
            ->get()
            ->groupBy('dossier_medical_id')
            ->map(fn ($consultationsPatient) => $consultationsPatient->first())
            ->sortBy(fn ($consultation) => strtolower((string) ($consultation?->dossierMedical?->nom ?? '')))
            ->values();

        return view('professional.workspace-patient-directory', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'patients' => $patients,
        ]);
    }

    public function patientHistory(Request $request): View|RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;

        if (! $dossierProfessionnel || ! $dossierProfessionnel->isValide()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre espace de travail est disponible après validation de votre profil professionnel.');
        }

        $search = trim((string) $request->query('q', ''));

        $consultationsQuery = ConsultationProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->with(['dossierMedical', 'patient', 'rendezVous.serviceProfessionnel'])
            ->latest();

        if ($search !== '') {
            $consultationsQuery->where(function ($query) use ($search): void {
                $query->whereHas('dossierMedical', function ($dossierQuery) use ($search): void {
                    $dossierQuery
                        ->where('numero_unique', 'like', '%'.$search.'%')
                        ->orWhere('nom', 'like', '%'.$search.'%')
                        ->orWhere('prenom', 'like', '%'.$search.'%')
                        ->orWhere('telephone', 'like', '%'.$search.'%');
                })->orWhereHas('patient', function ($patientQuery) use ($search): void {
                    $patientQuery->where('name', 'like', '%'.$search.'%');
                });
            });
        }

        $consultations = $consultationsQuery
            ->paginate(25)
            ->withQueryString();

        return view('professional.workspace-patient-history', [
            'dossierProfessionnel' => $dossierProfessionnel,
            'consultations' => $consultations,
            'search' => $search,
        ]);
    }

    public function accepterRendezVous(Request $request, RendezVousProfessionnel $rendezVousProfessionnel): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($rendezVousProfessionnel->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        if ($rendezVousProfessionnel->statut !== 'en_attente') {
            return back()->with('error', 'Ce rendez-vous a déjà été traité.');
        }

        $request->validate([
            'notes_professionnel' => ['nullable', 'string', 'max:1000'],
            'lien_teleconsultation_medecin' => [
                'nullable',
                'url',
                'max:500',
                $rendezVousProfessionnel->mode_deroulement === 'teleconsultation' ? 'required' : 'exclude',
            ],
        ]);

        DB::transaction(function () use ($request, $rendezVousProfessionnel, $dossierProfessionnel): void {
            $rendezVousProfessionnel->update([
                'statut' => 'accepte',
                'statut_acceptation' => 'accepte',
                'professionnel_user_id' => $dossierProfessionnel->user_id,
                'notes_professionnel' => $request->input('notes_professionnel'),
                'decision_le' => now(),
            ]);

            $service = $rendezVousProfessionnel->serviceProfessionnel;

            $consultation = ConsultationProfessionnelle::firstOrCreate(
                ['rendez_vous_professionnel_id' => $rendezVousProfessionnel->id],
                [
                    'dossier_professionnel_id' => $dossierProfessionnel->id,
                    'dossier_medical_id' => $rendezVousProfessionnel->dossier_medical_id,
                    'numero_dossier_reference' => $rendezVousProfessionnel->numero_dossier_reference,
                    'patient_user_id' => $rendezVousProfessionnel->patient_user_id,
                    'type_service' => match ($service?->type) {
                        'consultation' => 'consultation',
                        'examen' => 'examen',
                        default => 'autre',
                    },
                    'type_consultation' => match ($rendezVousProfessionnel->mode_deroulement ?? 'presentiel') {
                        'teleconsultation' => 'visio_teleconsultation',
                        default => 'presentiel',
                    },
                    'lien_teleconsultation' => $request->input('lien_teleconsultation_medecin'),
                    'statut' => 'brouillon',
                ]
            );

            if (($rendezVousProfessionnel->mode_deroulement ?? 'presentiel') === 'teleconsultation') {
                $consultation->update([
                    'type_consultation' => 'visio_teleconsultation',
                    'lien_teleconsultation' => $request->input('lien_teleconsultation_medecin'),
                ]);
            }

            if (! $rendezVousProfessionnel->facture) {
                FactureProfessionnelle::create([
                    'rendez_vous_professionnel_id' => $rendezVousProfessionnel->id,
                    'consultation_professionnelle_id' => $consultation->id,
                    'dossier_professionnel_id' => $dossierProfessionnel->id,
                    'professionnel_user_id' => $dossierProfessionnel->user_id,
                    'service_professionnel_id' => $service?->id,
                    'patient_user_id' => $rendezVousProfessionnel->patient_user_id,
                    'dossier_medical_id' => $rendezVousProfessionnel->dossier_medical_id,
                    'numero_dossier_reference' => $rendezVousProfessionnel->numero_dossier_reference,
                    'reference' => 'FACT-PRO-'.now()->format('YmdHis').'-'.mt_rand(100, 999),
                    'type_service' => $service?->type,
                    'type_facture' => match ($service?->type) {
                        'examen' => 'examen',
                        'consultation' => 'consultation',
                        default => 'autre',
                    },
                    'montant_total' => (float) ($service?->prix ?? 0),
                    'montant_couvert_mutuelle' => 0,
                    'montant_a_charge_patient' => (float) ($service?->prix ?? 0),
                    'statut' => 'emise',
                    'statut_mutuelle' => 'non_soumis',
                    'statut_backoffice' => 'en_attente',
                    'envoyee_backoffice' => true,
                    'soumise_backoffice_le' => now(),
                    'statut_paiement_patient' => 'en_attente',
                    'emise_le' => now(),
                ]);
            }
        });

        return back()->with('success', 'Rendez-vous accepté. Consultation et facture créées automatiquement.');
    }

    public function declinerRendezVous(Request $request, RendezVousProfessionnel $rendezVousProfessionnel): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($rendezVousProfessionnel->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        if ($rendezVousProfessionnel->statut !== 'en_attente') {
            return back()->with('error', 'Ce rendez-vous a déjà été traité.');
        }

        $request->validate([
            'notes_professionnel' => ['nullable', 'string', 'max:1000'],
        ]);

        $rendezVousProfessionnel->update([
            'statut' => 'decline',
            'statut_acceptation' => 'decline',
            'notes_professionnel' => $request->input('notes_professionnel'),
            'decision_le' => now(),
        ]);

        return back()->with('success', 'Rendez-vous décliné.');
    }

    public function editConsultation(ConsultationProfessionnelle $consultationProfessionnelle): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $consultationProfessionnelle->load([
            'rendezVous.patient',
            'rendezVous.serviceProfessionnel',
            'dossierMedical',
            'patient',
            'ordonnances',
            'examens.serviceProfessionnel.dossierProfessionnel.user',
            'documents.uploadedBy',
        ]);

        $servicesExamenInternes = $dossierProfessionnel->services()
            ->where('actif', true)
            ->where('type', 'examen')
            ->orderBy('nom')
            ->get();

        $professionnelsExamensExternes = DossierProfessionnel::query()
            ->valide()
            ->whereKeyNot($dossierProfessionnel->id)
            ->with([
                'user',
                'services' => fn ($query) => $query->where('actif', true)->where('type', 'examen')->orderBy('nom'),
            ])
            ->whereHas('services', fn ($query) => $query->where('actif', true)->where('type', 'examen'))
            ->get();

        $ordonnanceActive = $consultationProfessionnelle->ordonnances()->latest()->first();

        return view('professional.consultation-edit', [
            'consultation' => $consultationProfessionnelle,
            'ordonnanceActive' => $ordonnanceActive,
            'servicesExamenInternes' => $servicesExamenInternes,
            'professionnelsExamensExternes' => $professionnelsExamensExternes,
        ]);
    }

    public function patientDossier(DossierMedical $dossierMedical): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if(! $dossierMedical->actif, 403);

        // Ensure the professional has at least one consultation with this patient
        $hasRelation = ConsultationProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnel->id)
            ->where('dossier_medical_id', $dossierMedical->id)
            ->exists();

        abort_if(! $hasRelation, 403);

        $dossierMedical->load(['subscriptions', 'user']);

        return view('professional.patient-dossier', [
            'dossierMedical' => $dossierMedical,
            'dossierProfessionnel' => $dossierProfessionnel,
        ]);
    }

    public function storeDocument(Request $request, ConsultationProfessionnelle $consultationProfessionnelle): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $request->validate([
            'document' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        $file = $request->file('document');
        $path = $file->store('consultation-documents/'.$consultationProfessionnelle->id, 'public');

        ConsultationDocument::create([
            'consultation_professionnelle_id' => $consultationProfessionnelle->id,
            'uploaded_by_user_id' => Auth::id(),
            'nom_fichier' => $file->getClientOriginalName(),
            'file_path' => $path,
            'taille_octets' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'source' => 'professionnel',
        ]);

        return back()->with('success', 'Document ajouté avec succès.');
    }

    public function destroyDocument(ConsultationProfessionnelle $consultationProfessionnelle, ConsultationDocument $document): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }

    public function updateConsultation(UpdateConsultationProfessionnelleRequest $request, ConsultationProfessionnelle $consultationProfessionnelle): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $data = $request->validated();

        $consultationData = Arr::only($data, [
            'type_consultation',
            'lien_teleconsultation',
            'temperature',
            'tension_arterielle',
            'taux_glycemie',
            'poids',
            'symptomes',
            'conclusion',
            'diagnostic_medecin',
            'diagnostic',
            'recommandations',
            'ordonnance',
            'note_resultat',
            'observations',
            'statut',
        ]);

        if ($request->hasFile('fichier_resultat')) {
            if (filled($consultationProfessionnelle->fichier_resultat_path)) {
                Storage::disk('public')->delete($consultationProfessionnelle->fichier_resultat_path);
            }

            $consultationData['fichier_resultat_path'] = $request->file('fichier_resultat')
                ->store('consultation-resultats/'.$consultationProfessionnelle->id, 'public');
        }

        if (($consultationData['type_consultation'] ?? 'presentiel') !== 'visio_teleconsultation') {
            $consultationData['lien_teleconsultation'] = null;
        }

        if (($consultationData['statut'] ?? 'brouillon') === 'finalise') {
            $consultationData['finalise_le'] = now();
            $consultationProfessionnelle->rendezVous?->update([
                'statut' => 'termine',
            ]);
        } else {
            $consultationData['finalise_le'] = null;
        }

        $consultationProfessionnelle->update($consultationData);

        $ordonnancePayload = Arr::only($data, [
            'ordonnance_prescription',
            'ordonnance_recommandations',
            'ordonnance_instructions',
            'ordonnance_produits',
        ]);

        $hasOrdonnanceContent = collect($ordonnancePayload)
            ->filter(fn ($value) => filled($value))
            ->isNotEmpty();

        $ordonnance = $consultationProfessionnelle->ordonnances()->latest()->first();

        if ($hasOrdonnanceContent) {
            $produits = collect(preg_split('/\r\n|\r|\n/', (string) ($ordonnancePayload['ordonnance_produits'] ?? '')))
                ->map(fn ($ligne) => trim($ligne))
                ->filter()
                ->values()
                ->all();

            $ordonnanceData = [
                'dossier_medical_id' => $consultationProfessionnelle->dossier_medical_id,
                'professionnel_user_id' => $dossierProfessionnel->user_id,
                'prescription' => $ordonnancePayload['ordonnance_prescription'] ?? null,
                'recommandations' => $ordonnancePayload['ordonnance_recommandations'] ?? null,
                'instructions_complementaires' => $ordonnancePayload['ordonnance_instructions'] ?? null,
                'produits' => $produits,
                'statut' => ($consultationData['statut'] ?? 'brouillon') === 'finalise' ? 'finalisee' : 'brouillon',
            ];

            if ($ordonnance) {
                $ordonnance->update($ordonnanceData);
            } else {
                $ordonnance = OrdonnanceProfessionnelle::create([
                    'consultation_professionnelle_id' => $consultationProfessionnelle->id,
                    ...$ordonnanceData,
                ]);
            }
        }

        $mustCreateExamen = (bool) ($data['creer_examen'] ?? false);
        if ($mustCreateExamen) {
            $modeOrientation = $data['examen_mode_orientation'] ?? 'interne';

            $serviceExamen = null;
            $dossierDestination = $dossierProfessionnel;
            $professionnelDestinationId = $dossierProfessionnel->user_id;
            $commission = 0.00;
            $statutCommission = 'payee';
            $dossierRecommandeId = null;
            $recommandeParUserId = null;
            $statutExamen = 'en_cours';

            if ($modeOrientation === 'recommandation') {
                $dossierDestination = DossierProfessionnel::query()
                    ->valide()
                    ->find($data['examen_dossier_professionnel_cible_id'] ?? null);

                if (! $dossierDestination) {
                    return back()->withErrors([
                        'examen_dossier_professionnel_cible_id' => 'Veuillez sélectionner un professionnel de destination valide.',
                    ])->withInput();
                }

                if ($dossierDestination->id === $dossierProfessionnel->id) {
                    return back()->withErrors([
                        'examen_mode_orientation' => 'Pour un examen interne, choisissez le mode interne.',
                    ])->withInput();
                }

                $serviceExamen = $dossierDestination->services()
                    ->where('actif', true)
                    ->where('type', 'examen')
                    ->whereKey($data['examen_service_id'] ?? null)
                    ->first();

                if (! $serviceExamen) {
                    return back()->withErrors([
                        'examen_service_id' => 'Le service examen sélectionné ne correspond pas au professionnel recommandé.',
                    ])->withInput();
                }

                $professionnelDestinationId = $dossierDestination->user_id;
                $commission = round(((float) $serviceExamen->prix) * 0.10, 2);
                $statutCommission = 'en_attente';
                $dossierRecommandeId = $dossierProfessionnel->id;
                $recommandeParUserId = Auth::id();
                $statutExamen = 'demande';
            } else {
                $serviceExamen = $dossierProfessionnel->services()
                    ->where('actif', true)
                    ->where('type', 'examen')
                    ->whereKey($data['examen_service_id'] ?? null)
                    ->first();
            }

            if (! $serviceExamen) {
                return back()->withErrors([
                    'examen_service_id' => 'Veuillez sélectionner un service examen valide.',
                ])->withInput();
            }

            $noteOrientation = trim((string) ($data['examen_note_orientation'] ?? ''));
            if (filled($data['examen_whatsapp'] ?? null)) {
                $noteOrientation .= ($noteOrientation !== '' ? PHP_EOL : '').'Contact WhatsApp: '.$data['examen_whatsapp'];
            }

            $examLibelles = collect();

            if ($modeOrientation === 'interne') {
                $examLibelles = collect([$serviceExamen->nom ?? 'Examen']);
            } else {
                $examLibelles = collect(preg_split('/\r\n|\r|\n/', (string) ($data['examen_libelles'] ?? '')))
                    ->map(fn ($value) => trim((string) $value))
                    ->filter();

                if ($examLibelles->isEmpty() && filled($data['examen_libelle'] ?? null)) {
                    $examLibelles = collect([trim((string) $data['examen_libelle'])]);
                }

                if ($examLibelles->isEmpty()) {
                    $examLibelles = collect([$serviceExamen->nom ?? 'Examen']);
                }
            }

            $factureExamen = null;

            foreach ($examLibelles as $examLibelle) {
                $examen = ExamenProfessionnel::create([
                    'consultation_professionnelle_id' => $consultationProfessionnelle->id,
                    'service_professionnel_id' => $serviceExamen->id,
                    'dossier_professionnel_id' => $dossierDestination->id,
                    'dossier_professionnel_recommande_id' => $dossierRecommandeId,
                    'recommande_par_user_id' => $recommandeParUserId,
                    'professionnel_user_id' => $professionnelDestinationId,
                    'patient_user_id' => $consultationProfessionnelle->patient_user_id,
                    'dossier_medical_id' => $consultationProfessionnelle->dossier_medical_id,
                    'numero_dossier_reference' => $consultationProfessionnelle->numero_dossier_reference,
                    'libelle' => $examLibelle,
                    'note_orientation' => $noteOrientation !== '' ? $noteOrientation : null,
                    'statut' => $statutExamen,
                    'commission_recommandation_montant' => $commission,
                    'statut_commission' => $statutCommission,
                ]);

                if ($modeOrientation === 'interne') {
                    if (! $factureExamen) {
                        $factureExamen = $this->resolveExamFacture(
                            $consultationProfessionnelle,
                            $dossierProfessionnel,
                            $serviceExamen,
                            'Facture examen interne générée depuis la consultation.'
                        );
                    }

                    $examen->update([
                        'facture_professionnelle_id' => $factureExamen->id,
                    ]);
                }
            }
        }

        if ((bool) ($data['imprimer_ordonnance'] ?? false) && $ordonnance) {
            return redirect()->route('professional.workspace.consultation.ordonnance.print', $consultationProfessionnelle)
                ->with('success', 'Consultation mise à jour. Ordonnance générée pour impression.');
        }

        return redirect()->route('professional.workspace.dashboard')
            ->with('success', 'Fiche médicale mise à jour.');
    }

    public function generateTreatmentSuggestion(GenerateTreatmentSuggestionRequest $request, ConsultationProfessionnelle $consultationProfessionnelle, TreatmentSupportService $treatmentSupportService): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $suggestion = $treatmentSupportService->suggestForProfessional($consultationProfessionnelle, $request->validated());

        return back()
            ->withInput($request->except(['_token', '_method']))
            ->with('success', 'Suggestion de traitement générée.')
            ->with('treatment_ai_suggestion', $suggestion)
            ->with('treatment_ai_suggestion_generated', true);
    }

    public function printOrdonnance(ConsultationProfessionnelle $consultationProfessionnelle): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $ordonnance = $consultationProfessionnelle->ordonnances()->latest()->first();
        abort_if(! $ordonnance, 404);

        $consultationProfessionnelle->load(['patient', 'dossierMedical', 'rendezVous.serviceProfessionnel']);

        return view('professional.ordonnance-print', [
            'consultation' => $consultationProfessionnelle,
            'ordonnance' => $ordonnance,
            'dossierProfessionnel' => $dossierProfessionnel,
        ]);
    }

    public function printConsultation(ConsultationProfessionnelle $consultationProfessionnelle): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $consultationProfessionnelle->load([
            'patient',
            'dossierMedical',
            'rendezVous.serviceProfessionnel',
            'documents.uploadedBy',
        ]);

        return view('professional.consultation-print', [
            'consultation' => $consultationProfessionnelle,
            'dossierProfessionnel' => $dossierProfessionnel,
        ]);
    }

    public function printSummary(ConsultationProfessionnelle $consultationProfessionnelle): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($consultationProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $consultationProfessionnelle->load([
            'patient',
            'dossierMedical',
            'rendezVous.serviceProfessionnel',
            'ordonnances',
            'examens.serviceProfessionnel.dossierProfessionnel.user',
            'documents.uploadedBy',
        ]);

        $ordonnance = $consultationProfessionnelle->ordonnances()->latest()->first();

        return view('professional.consultation-summary-print', [
            'consultation' => $consultationProfessionnelle,
            'ordonnance' => $ordonnance,
            'dossierProfessionnel' => $dossierProfessionnel,
        ]);
    }

    public function printFacture(FactureProfessionnelle $factureProfessionnelle): View
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($factureProfessionnelle->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        $factureProfessionnelle->load([
            'dossierMedical',
            'patient',
            'consultation',
            'serviceProfessionnel',
            'rendezVous',
        ]);

        return view('professional.facture-print', [
            'facture' => $factureProfessionnelle,
            'dossierProfessionnel' => $dossierProfessionnel,
        ]);
    }

    public function accepterExamen(ExamenProfessionnel $examenProfessionnel): RedirectResponse
    {
        $dossierProfessionnel = Auth::user()?->dossierProfessionnel;
        abort_if(! $dossierProfessionnel || ! $dossierProfessionnel->isValide(), 403);
        abort_if($examenProfessionnel->dossier_professionnel_id !== $dossierProfessionnel->id, 403);

        if ($examenProfessionnel->statut !== 'demande') {
            return back()->with('error', 'Cette demande d\'examen a déjà été traitée.');
        }

        $serviceExamen = $dossierProfessionnel->services()
            ->where('actif', true)
            ->where('type', 'examen')
            ->whereKey($examenProfessionnel->service_professionnel_id)
            ->first();

        if (! $serviceExamen) {
            return back()->with('error', 'Le service examen associé est indisponible ou inactif.');
        }

        $consultation = $examenProfessionnel->consultation;
        if (! $consultation) {
            return back()->with('error', 'La consultation associée à cette demande est introuvable.');
        }

        $factureExamen = $this->resolveExamFacture(
            $consultation,
            $dossierProfessionnel,
            $serviceExamen,
            'Facture examen recommandée validée par le professionnel destinataire.'
        );

        $examenProfessionnel->update([
            'facture_professionnelle_id' => $factureExamen->id,
            'statut' => 'en_cours',
        ]);

        $patient = $examenProfessionnel->patient;
        if ($patient) {
            $patient->notify(new ExamenAccepteNotification($examenProfessionnel));
        }

        return back()->with('success', 'Demande d\'examen acceptée. Facture générée et patient notifié.');
    }

    private function resolveExamFacture(
        ConsultationProfessionnelle $consultationProfessionnelle,
        DossierProfessionnel $dossierProfessionnel,
        ServiceProfessionnel $serviceExamen,
        string $notes
    ): FactureProfessionnelle {
        $existingFactureRdv = FactureProfessionnelle::query()
            ->where('rendez_vous_professionnel_id', $consultationProfessionnelle->rendez_vous_professionnel_id)
            ->first();

        if ($existingFactureRdv) {
            if ((int) $existingFactureRdv->dossier_professionnel_id !== (int) $dossierProfessionnel->id) {
                throw ValidationException::withMessages([
                    'examen_service_id' => 'Validation requise: exécutez les migrations pour autoriser une facture examen dédiée sans rendez-vous.',
                ]);
            }

            return $existingFactureRdv;
        }

        return FactureProfessionnelle::create([
            'rendez_vous_professionnel_id' => $consultationProfessionnelle->rendez_vous_professionnel_id,
            'consultation_professionnelle_id' => $consultationProfessionnelle->id,
            'dossier_professionnel_id' => $dossierProfessionnel->id,
            'professionnel_user_id' => $dossierProfessionnel->user_id,
            'service_professionnel_id' => $serviceExamen->id,
            'patient_user_id' => $consultationProfessionnelle->patient_user_id,
            'dossier_medical_id' => $consultationProfessionnelle->dossier_medical_id,
            'numero_dossier_reference' => $consultationProfessionnelle->numero_dossier_reference,
            'reference' => 'FACT-EXAM-'.now()->format('YmdHis').'-'.mt_rand(100, 999),
            'type_service' => 'examen',
            'type_facture' => 'examen',
            'montant_total' => (float) ($serviceExamen->prix ?? 0),
            'montant_couvert_mutuelle' => 0,
            'montant_a_charge_patient' => (float) ($serviceExamen->prix ?? 0),
            'statut' => 'emise',
            'statut_mutuelle' => 'non_soumis',
            'statut_backoffice' => 'en_attente',
            'envoyee_backoffice' => true,
            'soumise_backoffice_le' => now(),
            'statut_paiement_patient' => 'en_attente',
            'emise_le' => now(),
            'notes' => $notes,
        ]);
    }

    private function eligibleInvoicesForWithdrawal(int $dossierProfessionnelId): Collection
    {
        return FactureProfessionnelle::query()
            ->where('dossier_professionnel_id', $dossierProfessionnelId)
            ->where('statut_backoffice', 'paye')
            ->whereDoesntHave('retraits', function ($query): void {
                $query->whereIn('statut', ['en_attente', 'approuve', 'paye']);
            })
            ->orderBy('emise_le')
            ->get();
    }

    private function backofficeFeedbackUnreadCount(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return (int) $user->unreadNotifications()
            ->whereIn('data->type', [
                'facture_backoffice_mise_a_jour',
                'retrait_professionnel_traite',
            ])
            ->count();
    }
}
