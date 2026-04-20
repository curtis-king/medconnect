<?php

namespace App\Http\Controllers;

use App\Models\ConsultationDocument;
use App\Models\ConsultationProfessionnelle;
use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use App\Models\ExamenProfessionnel;
use App\Models\FactureProfessionnelle;
use App\Models\Frais;
use App\Models\OrdonnanceProfessionnelle;
use App\Models\Paiement;
use App\Models\RendezVousProfessionnel;
use App\Models\SoumissionMutuelle;
use App\Models\Subscription;
use App\Notifications\TraitementSuiviAnalyseNotification;
use App\Services\TreatmentSupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserPatientPortalController extends Controller
{
    public function validationStatus(): View
    {
        $user = Auth::user();

        $medicalDossiers = DossierMedical::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->get();

        $professionalDossier = DossierProfessionnel::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->first();

        return view('user.validation-status', [
            'medicalDossiers' => $medicalDossiers,
            'professionalDossier' => $professionalDossier,
        ]);
    }

    public function dashboard(): View
    {
        $user = Auth::user();
        $managedMedicalDossiers = DossierMedical::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->get();

        $fraisReabonnementPatient = Frais::query()->where('type', 'reabonnement')->first();
        $fraisReabonnementPro = Frais::query()->where('type', 'reabonnement_pro')->first();
        $estimatedMedicalMonthlyCharge = (float) ($fraisReabonnementPatient?->prix ?? 0) * $managedMedicalDossiers->count();
        $estimatedProfessionalMonthlyCharge = $user?->dossierProfessionnel
            ? (float) ($fraisReabonnementPro?->prix ?? 0)
            : 0.0;
        $pendingMedicalRenewalPayments = Paiement::query()
            ->where('type_paiement', 'reabonnement')
            ->where('mode_paiement', 'mobile_money')
            ->where('statut', 'en_attente')
            ->whereHas('dossierMedical', fn ($query) => $query->where('user_id', $user?->id))
            ->latest()
            ->get();

        $dossierMedical = $managedMedicalDossiers->first() ?? $user?->dossierMedical;
        $dossierProfessionnel = $user?->dossierProfessionnel;
        $medicalSubscription = $dossierMedical?->activeSubscription()->first();
        $professionalSubscription = $dossierProfessionnel?->activeSubscription()->first();

        $facturesBase = FactureProfessionnelle::query()
            ->where('patient_user_id', $user?->id);

        $documentsCount = $this->buildDocumentsCollection($user?->id)->count();

        return view('dashboard', [
            'myMedicalDossier' => $dossierMedical,
            'managedMedicalDossiers' => $managedMedicalDossiers,
            'pendingMedicalRenewalPayment' => $pendingMedicalRenewalPayments->first(),
            'pendingMedicalRenewalReferences' => $pendingMedicalRenewalPayments
                ->pluck('reference_paiement')
                ->filter()
                ->values(),
            'myProfessionalDossier' => $dossierProfessionnel,
            'medicalSubscription' => $medicalSubscription,
            'professionalSubscription' => $professionalSubscription,
            'estimatedMedicalMonthlyCharge' => $estimatedMedicalMonthlyCharge,
            'estimatedProfessionalMonthlyCharge' => $estimatedProfessionalMonthlyCharge,
            'estimatedTotalMonthlyCharge' => $estimatedMedicalMonthlyCharge + $estimatedProfessionalMonthlyCharge,
            'reabonnementUnitPatient' => (float) ($fraisReabonnementPatient?->prix ?? 0),
            'reabonnementUnitProfessional' => (float) ($fraisReabonnementPro?->prix ?? 0),
            'medicalSubscriptionDaysRemaining' => $medicalSubscription?->days_remaining ?? 0,
            'professionalSubscriptionDaysRemaining' => $professionalSubscription?->days_remaining ?? 0,
            'pendingPaymentsCount' => (clone $facturesBase)->where('statut_paiement_patient', 'en_attente')->count(),
            'totalPaidExpenses' => (float) (clone $facturesBase)->where('statut_paiement_patient', 'paye')->sum('montant_total'),
            'upcomingAppointmentsCount' => RendezVousProfessionnel::query()
                ->where('patient_user_id', $user?->id)
                ->whereIn('statut', ['en_attente', 'accepte'])
                ->where('date_proposee', '>=', now())
                ->count(),
            'documentsCount' => $documentsCount,
            'unreadNotificationsCount' => $user?->unreadNotifications()->count() ?? 0,
        ]);
    }

    public function renewMedicalSubscription(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return back()->with('error', 'Aucun dossier medical trouve pour ce compte.');
        }

        $payload = $request->validate([
            'nombre_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'dossier_reference' => ['nullable', 'string', 'max:120'],
            'mode_paiement' => ['required', 'in:mobile_money,carte_bancaire'],
            'mobile_money_provider' => ['nullable', 'required_if:mode_paiement,mobile_money', 'in:mtn,airtel'],
            'mobile_money_number' => ['nullable', 'required_if:mode_paiement,mobile_money', 'regex:/^[0-9]{8,15}$/'],
            'card_holder_name' => ['nullable', 'required_if:mode_paiement,carte_bancaire', 'string', 'max:120'],
            'stripe_payment_method_id' => ['nullable', 'string', 'max:255'],
        ]);

        $dossierMedical = $this->resolveManagedMedicalDossier($user?->id, $payload['dossier_reference'] ?? null);

        if (! $dossierMedical) {
            return back()->with('error', 'Aucun dossier medical correspondant a la reference fournie pour ce compte.');
        }

        $fraisReabonnement = Frais::query()->where('type', 'reabonnement')->first();
        if (! $fraisReabonnement) {
            return back()->with('error', 'Frais de reabonnement patient introuvable.');
        }

        $paymentStatus = 'paye';
        $paymentReference = 'PAY-'.now()->format('YmdHis').'-'.strtoupper(Str::random(5));
        $paymentNotes = null;
        $isMobileMoney = $payload['mode_paiement'] === 'mobile_money';
        $mobileMoneyVerificationStatus = 'paid';

        if ($isMobileMoney) {
            $paymentReference = strtoupper((string) $payload['mobile_money_provider']).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));
            $paymentStatus = 'en_attente';
            $paymentNotes = 'Mobile Money simule et valide: '
                .strtoupper((string) $payload['mobile_money_provider'])
                .' / '
                .(string) $payload['mobile_money_number'];

            $mobileMoneyVerificationStatus = $this->verifyMobileMoneyTransaction(
                provider: (string) $payload['mobile_money_provider'],
                phoneNumber: (string) $payload['mobile_money_number'],
                paymentReference: $paymentReference,
            );

            if ($mobileMoneyVerificationStatus === 'paid') {
                $paymentStatus = 'paye';
                $paymentNotes = 'Mobile Money confirme par verification API: '
                    .strtoupper((string) $payload['mobile_money_provider'])
                    .' / '
                    .(string) $payload['mobile_money_number'];
            }

            if ($mobileMoneyVerificationStatus === 'failed') {
                $paymentStatus = 'annule';
                $paymentNotes = 'Transaction Mobile Money rejetee par verification API.';
            }
        }

        if ($payload['mode_paiement'] === 'carte_bancaire') {
            $stripeConfigured = filled(config('services.stripe.secret'));
            $paymentReference = $stripeConfigured
                ? 'STRIPE-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
                : 'STRIPE-SIM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));
            $paymentNotes = $stripeConfigured
                ? 'Flux carte Stripe simule et valide.'
                : 'Flux carte en mode simulation: ajouter STRIPE_KEY/STRIPE_SECRET pour activer Stripe.';
        }

        $periodStart = now();
        $periodEnd = now()->copy()->addMonths((int) $payload['nombre_mois'])->subDay();
        $lastSubscription = Subscription::query()
            ->where('dossier_medical_id', $dossierMedical->id)
            ->where('statut', '!=', 'annule')
            ->latest('date_fin')
            ->first();

        if ($lastSubscription && $lastSubscription->date_fin && $lastSubscription->date_fin->gte(now()->toDateString())) {
            $periodStart = $lastSubscription->date_fin->copy()->addDay();
            $periodEnd = $periodStart->copy()->addMonths((int) $payload['nombre_mois'])->subDay();
        }

        $paiement = Paiement::query()->create([
            'dossier_medical_id' => $dossierMedical->id,
            'frais_inscription_id' => $fraisReabonnement->id,
            'type_paiement' => 'reabonnement',
            'montant' => (float) $fraisReabonnement->prix * (int) $payload['nombre_mois'],
            'periode_debut' => $periodStart,
            'periode_fin' => $periodEnd,
            'nombre_mois' => (int) $payload['nombre_mois'],
            'statut' => $paymentStatus,
            'mode_paiement' => $payload['mode_paiement'] === 'carte_bancaire' ? 'carte' : 'mobile_money',
            'reference_paiement' => $paymentReference,
            'notes' => $paymentNotes,
            'date_encaissement' => $paymentStatus === 'paye' ? now() : null,
            'date_echeance' => now()->addMinutes(20),
        ]);

        if ($paymentStatus === 'annule') {
            return back()->with('error', 'Transaction Mobile Money refusee. Veuillez verifier les informations puis reessayer.');
        }

        if ($paymentStatus === 'en_attente') {
            return back()->with('success', 'Paiement Mobile Money initie. Verification en cours, veuillez patienter...')
                ->with('pending_medical_renewal_reference', $paiement->reference_paiement);
        }

        $subscription = Subscription::createWithAutoDate(
            dossierMedicalId: $dossierMedical->id,
            fraisId: $fraisReabonnement->id,
            nombreMois: (int) $payload['nombre_mois'],
            modePaiement: $payload['mode_paiement'],
            encaisseParUserId: $user?->id,
        );

        $subscription->update([
            'reference_paiement' => $paymentReference,
            'notes' => $paymentNotes,
        ]);

        $relationLabel = $dossierMedical->est_personne_a_charge
            ? ($dossierMedical->lien_avec_responsable_label ?? ucfirst((string) $dossierMedical->lien_avec_responsable))
            : 'Dossier personnel';

        return back()->with(
            'success',
            'Transaction validee (simulation). Reabonnement patient active pour '
            .$dossierMedical->nom_complet
            .' ['.$dossierMedical->numero_unique.'] - '
            .$relationLabel
            .'. Reference: '
            .$paymentReference
        );
    }

    public function renewAllSubscriptions(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $payload = $request->validate([
            'nombre_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'mode_paiement' => ['required', 'in:mobile_money,carte_bancaire'],
            'mobile_money_provider' => ['nullable', 'required_if:mode_paiement,mobile_money', 'in:mtn,airtel'],
            'mobile_money_number' => ['nullable', 'required_if:mode_paiement,mobile_money', 'regex:/^[0-9]{8,15}$/'],
            'card_holder_name' => ['nullable', 'required_if:mode_paiement,carte_bancaire', 'string', 'max:120'],
            'stripe_payment_method_id' => ['nullable', 'string', 'max:255'],
            'include_professional' => ['nullable', 'boolean'],
        ]);

        $managedMedicalDossiers = DossierMedical::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->get();

        if ($managedMedicalDossiers->isEmpty()) {
            return back()->with('error', 'Aucun dossier medical rattache a ce compte.');
        }

        $fraisReabonnement = Frais::query()->where('type', 'reabonnement')->first();

        if (! $fraisReabonnement) {
            return back()->with('error', 'Frais de reabonnement patient introuvable.');
        }

        $paidCount = 0;
        $pendingCount = 0;
        $failedCount = 0;
        $pendingReferences = [];
        $nombreMois = (int) $payload['nombre_mois'];
        $modePaiement = (string) $payload['mode_paiement'];

        foreach ($managedMedicalDossiers as $dossierMedical) {
            $paymentReference = $modePaiement === 'mobile_money'
                ? strtoupper((string) $payload['mobile_money_provider']).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
                : 'STRIPE-SIM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));

            $paymentStatus = 'paye';
            $paymentNotes = $modePaiement === 'mobile_money'
                ? 'Mobile Money reabonnement global: '.strtoupper((string) $payload['mobile_money_provider']).' / '.(string) $payload['mobile_money_number']
                : 'Flux carte en mode simulation: reglement global des reabonnements.';

            if ($modePaiement === 'mobile_money') {
                $paymentStatus = 'en_attente';

                $verificationStatus = $this->verifyMobileMoneyTransaction(
                    provider: (string) $payload['mobile_money_provider'],
                    phoneNumber: (string) $payload['mobile_money_number'],
                    paymentReference: $paymentReference,
                );

                if ($verificationStatus === 'paid') {
                    $paymentStatus = 'paye';
                    $paymentNotes = 'Mobile Money confirme par verification API: '
                        .strtoupper((string) $payload['mobile_money_provider'])
                        .' / '
                        .(string) $payload['mobile_money_number'];
                }

                if ($verificationStatus === 'failed') {
                    $paymentStatus = 'annule';
                    $paymentNotes = 'Transaction Mobile Money rejetee par verification API.';
                }
            }

            $paiement = Paiement::query()->create([
                'dossier_medical_id' => $dossierMedical->id,
                'frais_inscription_id' => $fraisReabonnement->id,
                'type_paiement' => 'reabonnement',
                'montant' => (float) $fraisReabonnement->prix * $nombreMois,
                'periode_debut' => now(),
                'periode_fin' => now()->copy()->addMonths($nombreMois)->subDay(),
                'nombre_mois' => $nombreMois,
                'statut' => $paymentStatus,
                'mode_paiement' => $modePaiement === 'carte_bancaire' ? 'carte' : 'mobile_money',
                'reference_paiement' => $paymentReference,
                'notes' => $paymentNotes,
                'date_encaissement' => $paymentStatus === 'paye' ? now() : null,
                'date_echeance' => now()->addMinutes(20),
            ]);

            if ($paymentStatus === 'annule') {
                $failedCount++;

                continue;
            }

            if ($paymentStatus === 'en_attente') {
                $pendingCount++;
                $pendingReferences[] = (string) $paiement->reference_paiement;

                continue;
            }

            $subscription = Subscription::createWithAutoDate(
                dossierMedicalId: $dossierMedical->id,
                fraisId: $fraisReabonnement->id,
                nombreMois: $nombreMois,
                modePaiement: $modePaiement,
                encaisseParUserId: $user?->id,
            );

            $subscription->update([
                'reference_paiement' => $paymentReference,
                'notes' => $paymentNotes,
            ]);

            $paidCount++;
        }

        $includeProfessional = filter_var($payload['include_professional'] ?? false, FILTER_VALIDATE_BOOL);

        if ($includeProfessional && $user?->dossierProfessionnel) {
            $fraisReabonnementPro = Frais::query()->where('type', 'reabonnement_pro')->first();

            if ($fraisReabonnementPro) {
                $paymentReferencePro = $modePaiement === 'mobile_money'
                    ? strtoupper((string) ($payload['mobile_money_provider'] ?? 'mtn')).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
                    : 'STRIPE-SIM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));

                $paymentNotesPro = $modePaiement === 'mobile_money'
                    ? 'Mobile Money reabonnement professionnel global: '.strtoupper((string) ($payload['mobile_money_provider'] ?? 'mtn')).' / '.(string) ($payload['mobile_money_number'] ?? '')
                    : 'Flux carte en mode simulation: reglement global professionnel.';

                $subscriptionPro = \App\Models\SubscriptionProfessionnelle::createWithAutoDate(
                    dossierProfessionnelId: (int) $user->dossierProfessionnel->id,
                    fraisId: (int) $fraisReabonnementPro->id,
                    nombreMois: $nombreMois,
                    modePaiement: $modePaiement,
                    encaisseParUserId: $user?->id,
                );

                $subscriptionPro->update([
                    'reference_paiement' => $paymentReferencePro,
                    'notes' => $paymentNotesPro,
                ]);
            }
        }

        if ($pendingCount > 0) {
            return back()
                ->with('success', $paidCount.' reabonnement(s) actives. '.$pendingCount.' en attente de verification API Mobile Money.')
                ->with('pending_medical_renewal_references', $pendingReferences)
                ->with('pending_medical_renewal_reference', $pendingReferences[0] ?? null);
        }

        if ($failedCount > 0 && $paidCount === 0) {
            return back()->with('error', 'Aucun reabonnement n a pu etre regle. '.$failedCount.' transaction(s) refusee(s).');
        }

        return back()->with('success', 'Reglement global termine. '.$paidCount.' reabonnement(s) active(s).'.($failedCount > 0 ? ' '.$failedCount.' transaction(s) refusee(s).' : ''));
    }

    public function medicalRenewalPaymentStatus(Request $request): JsonResponse
    {
        $user = Auth::user();

        $payload = $request->validate([
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $paiement = Paiement::query()
            ->where('reference_paiement', (string) $payload['reference'])
            ->where('type_paiement', 'reabonnement')
            ->whereHas('dossierMedical', fn ($query) => $query->where('user_id', $user?->id))
            ->latest()
            ->first();

        if (! $paiement) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Paiement introuvable pour ce compte.',
            ], 404);
        }

        if ($paiement->statut === 'paye') {
            return response()->json([
                'status' => 'paid',
                'message' => 'Paiement confirme. Votre abonnement est actif.',
            ]);
        }

        if ($paiement->statut === 'annule') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Paiement refuse par l operateur.',
            ]);
        }

        if ($paiement->mode_paiement !== 'mobile_money') {
            return response()->json([
                'status' => 'pending',
                'message' => 'Paiement en attente de verification.',
            ]);
        }

        $notes = (string) $paiement->notes;
        $provider = str_contains($notes, 'AIRTEL') ? 'airtel' : 'mtn';
        $phoneNumber = $this->extractPhoneNumberFromNotes($notes);

        $verificationStatus = $this->verifyMobileMoneyTransaction(
            provider: $provider,
            phoneNumber: $phoneNumber,
            paymentReference: (string) $paiement->reference_paiement,
        );

        if ($verificationStatus === 'pending') {
            return response()->json([
                'status' => 'pending',
                'message' => 'En attente de reponse API Mobile Money...',
            ]);
        }

        if ($verificationStatus === 'failed') {
            $paiement->update([
                'statut' => 'annule',
                'notes' => trim($notes.' | Verification API: transaction refusee.'),
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Paiement refuse par l operateur.',
            ]);
        }

        $paiement->update([
            'statut' => 'paye',
            'date_encaissement' => now(),
            'notes' => trim($notes.' | Verification API: transaction confirmee.'),
        ]);

        $subscription = Subscription::createWithAutoDate(
            dossierMedicalId: (int) $paiement->dossier_medical_id,
            fraisId: (int) $paiement->frais_inscription_id,
            nombreMois: (int) $paiement->nombre_mois,
            modePaiement: 'mobile_money',
            encaisseParUserId: $user?->id,
        );

        $subscription->update([
            'reference_paiement' => $paiement->reference_paiement,
            'notes' => trim(((string) $subscription->notes).' | Activation apres verification Mobile Money.'),
        ]);

        return response()->json([
            'status' => 'paid',
            'message' => 'Paiement confirme. Abonnement active.',
        ]);
    }

    private function verifyMobileMoneyTransaction(string $provider, string $phoneNumber, string $paymentReference): string
    {
        $apiKey = (string) config('services.mobile_money.api_key');

        if ($apiKey === '') {
            // Simulation mode: approve immediately when no API credentials are configured.
            return 'paid';
        }

        try {
            // Placeholder de verification API. Remplacer ici par l appel reel MTN/Airtel.
            return 'paid';
        } catch (\Throwable $throwable) {
            Log::warning('Erreur verification Mobile Money', [
                'provider' => $provider,
                'phone_number' => $phoneNumber,
                'reference' => $paymentReference,
                'message' => $throwable->getMessage(),
            ]);

            return 'pending';
        }
    }

    private function extractPhoneNumberFromNotes(string $notes): string
    {
        preg_match('/([0-9]{8,15})/', $notes, $matches);

        return (string) ($matches[1] ?? '');
    }

    public function renewProfessionalSubscription(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $dossierProfessionnel = $user?->dossierProfessionnel;

        if (! $dossierProfessionnel) {
            return back()->with('error', 'Aucun dossier professionnel trouve pour ce compte.');
        }

        $payload = $request->validate([
            'nombre_mois' => ['required', 'integer', 'min:1', 'max:12'],
            'mode_paiement' => ['required', 'in:mobile_money,carte_bancaire'],
            'mobile_money_provider' => ['nullable', 'required_if:mode_paiement,mobile_money', 'in:mtn,airtel'],
            'mobile_money_number' => ['nullable', 'required_if:mode_paiement,mobile_money', 'regex:/^[0-9]{8,15}$/'],
            'card_holder_name' => ['nullable', 'required_if:mode_paiement,carte_bancaire', 'string', 'max:120'],
            'stripe_payment_method_id' => ['nullable', 'string', 'max:255'],
        ]);

        $fraisReabonnementPro = Frais::query()->where('type', 'reabonnement_pro')->first();
        if (! $fraisReabonnementPro) {
            return back()->with('error', 'Frais de reabonnement professionnel introuvable.');
        }

        $stripeConfigured = filled(config('services.stripe.secret'));
        $paymentReference = $payload['mode_paiement'] === 'mobile_money'
            ? strtoupper((string) $payload['mobile_money_provider']).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
            : ($stripeConfigured
                ? 'STRIPE-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
                : 'STRIPE-SIM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)));

        $paymentNotes = $payload['mode_paiement'] === 'mobile_money'
            ? 'Mobile Money simule et valide: '.strtoupper((string) $payload['mobile_money_provider']).' / '.(string) $payload['mobile_money_number']
            : ($stripeConfigured
                ? 'Flux carte Stripe simule et valide.'
                : 'Flux carte en mode simulation: ajouter STRIPE_KEY/STRIPE_SECRET pour activer Stripe.');

        $subscription = \App\Models\SubscriptionProfessionnelle::createWithAutoDate(
            dossierProfessionnelId: $dossierProfessionnel->id,
            fraisId: $fraisReabonnementPro->id,
            nombreMois: (int) $payload['nombre_mois'],
            modePaiement: $payload['mode_paiement'],
            encaisseParUserId: $user?->id,
        );

        $subscription->update([
            'reference_paiement' => $paymentReference,
            'notes' => $paymentNotes,
        ]);

        return back()->with(
            'success',
            'Transaction validee (simulation). Reabonnement professionnel active. Reference: '.$paymentReference
        );
    }

    public function payments(): View|RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous devez d\'abord creer votre dossier medical.');
        }

        $facturesEnAttente = FactureProfessionnelle::query()
            ->where('patient_user_id', $user?->id)
            ->where('statut_paiement_patient', 'en_attente')
            ->with(['serviceProfessionnel', 'dossierProfessionnel.user', 'soumissionsMutuelle'])
            ->latest()
            ->paginate(12, ['*'], 'pending_page');

        $facturesRecentes = FactureProfessionnelle::query()
            ->where('patient_user_id', $user?->id)
            ->with(['serviceProfessionnel', 'dossierProfessionnel.user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('patient.payments', [
            'dossierMedical' => $dossierMedical,
            'facturesEnAttente' => $facturesEnAttente,
            'facturesRecentes' => $facturesRecentes,
            'activeSubscription' => $dossierMedical->activeSubscription()->first(),
        ]);
    }

    public function payPersonally(Request $request, FactureProfessionnelle $factureProfessionnelle): RedirectResponse
    {
        $user = Auth::user();
        abort_if((int) $factureProfessionnelle->patient_user_id !== (int) $user?->id, 403);

        $latestSubmission = SoumissionMutuelle::query()
            ->where('facture_professionnelle_id', $factureProfessionnelle->id)
            ->latest('id')
            ->first();

        if (
            ($latestSubmission && in_array($latestSubmission->statut, ['approuve', 'partiel'], true)) ||
            in_array((string) $factureProfessionnelle->statut_backoffice, ['valide', 'paye'], true)
        ) {
            return back()->with('error', 'Le paiement personnel n est plus disponible: la prise en charge mutuelle est deja validee/payée.');
        }

        $payload = $request->validate([
            'mode_paiement' => ['required', 'in:mobile_money,carte'],
            'mobile_money_provider' => ['nullable', 'required_if:mode_paiement,mobile_money', 'in:mtn,airtel'],
            'mobile_money_number' => ['nullable', 'required_if:mode_paiement,mobile_money', 'regex:/^[0-9]{8,15}$/'],
            'card_holder_name' => ['nullable', 'required_if:mode_paiement,carte', 'string', 'max:120'],
            'stripe_payment_method_id' => ['nullable', 'string', 'max:255'],
        ]);

        if ($factureProfessionnelle->statut_paiement_patient === 'paye') {
            return back()->with('success', 'Cette facture est deja marquee comme payee.');
        }

        $paymentReference = null;
        $paymentNotes = null;

        if ($payload['mode_paiement'] === 'mobile_money') {
            $paymentReference = strtoupper((string) $payload['mobile_money_provider']).'-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));
            $paymentNotes = 'Paiement personnel Mobile Money simule et valide: '
                .strtoupper((string) $payload['mobile_money_provider'])
                .' / '
                .(string) $payload['mobile_money_number'];
        }

        if ($payload['mode_paiement'] === 'carte') {
            $stripeConfigured = filled(config('services.stripe.secret'));
            $paymentReference = $stripeConfigured
                ? 'STRIPE-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4))
                : 'STRIPE-SIM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4));
            $paymentNotes = $stripeConfigured
                ? 'Paiement carte personnel Stripe simule et valide.'
                : 'Paiement carte personnel en simulation: configurer STRIPE_KEY/STRIPE_SECRET pour activer Stripe.';
        }

        $factureProfessionnelle->update([
            'statut' => 'payee',
            'statut_paiement_patient' => 'paye',
            'mode_paiement' => $payload['mode_paiement'],
            'reference_paiement' => $paymentReference,
            'notes' => $paymentNotes,
            'payee_le' => now(),
        ]);

        return back()->with('success', 'Paiement personnel enregistre avec succes.');
    }

    public function submitToBackoffice(FactureProfessionnelle $factureProfessionnelle): RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        abort_if((int) $factureProfessionnelle->patient_user_id !== (int) $user?->id, 403);

        if (! $dossierMedical) {
            return back()->with('error', 'Dossier medical introuvable.');
        }

        $activeSubscription = $dossierMedical->activeSubscription()->first();
        if (! $activeSubscription) {
            return back()->with('error', 'Aucun abonnement actif. Veuillez regulariser votre abonnement avant soumission.');
        }

        $existingSubmission = SoumissionMutuelle::query()
            ->where('facture_professionnelle_id', $factureProfessionnelle->id)
            ->whereIn('statut', ['soumis', 'en_traitement', 'approuve', 'partiel'])
            ->exists();

        if ($existingSubmission) {
            return back()->with('success', 'Cette facture est deja soumise au backoffice.');
        }

        DB::transaction(function () use ($factureProfessionnelle, $dossierMedical, $activeSubscription): void {
            SoumissionMutuelle::create([
                'facture_professionnelle_id' => $factureProfessionnelle->id,
                'dossier_medical_id' => $dossierMedical->id,
                'subscription_id' => $activeSubscription->id,
                'reference' => 'SMM-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)),
                'montant_soumis' => (float) $factureProfessionnelle->montant_total,
                'statut' => 'soumis',
                'date_soumission' => now(),
            ]);

            $factureProfessionnelle->update([
                'statut_mutuelle' => 'en_attente',
                'statut_backoffice' => 'en_attente',
                'envoyee_backoffice' => true,
                'soumise_backoffice_le' => now(),
            ]);
        });

        return back()->with('success', 'Facture soumise au backoffice avec notification automatique.');
    }

    public function cancelBackofficeSubmission(FactureProfessionnelle $factureProfessionnelle): RedirectResponse
    {
        $user = Auth::user();

        abort_if((int) $factureProfessionnelle->patient_user_id !== (int) $user?->id, 403);

        $latestSubmission = SoumissionMutuelle::query()
            ->where('facture_professionnelle_id', $factureProfessionnelle->id)
            ->latest('id')
            ->first();

        if (! $latestSubmission) {
            return back()->with('error', 'Aucune demande soumise a annuler.');
        }

        if (in_array($latestSubmission->statut, ['approuve', 'partiel'], true) || $factureProfessionnelle->statut_backoffice === 'paye') {
            return back()->with('error', 'Cette demande est deja validee/payée et ne peut plus etre annulee.');
        }

        if (! in_array($latestSubmission->statut, ['soumis', 'en_traitement'], true)) {
            return back()->with('error', 'Cette demande ne peut pas etre annulee dans son statut actuel.');
        }

        DB::transaction(function () use ($latestSubmission, $factureProfessionnelle): void {
            $latestSubmission->update([
                'statut' => 'rejete',
                'date_traitement' => now(),
                'motif_rejet' => 'Demande annulee par le patient avant validation.',
            ]);

            $factureProfessionnelle->update([
                'statut_mutuelle' => 'non_soumis',
                'statut_backoffice' => 'en_attente',
                'envoyee_backoffice' => false,
                'soumise_backoffice_le' => null,
            ]);
        });

        return back()->with('success', 'La demande de prise en charge a ete annulee.');
    }

    public function finances(): View|RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous devez d\'abord creer votre dossier medical.');
        }

        $depensesPayees = FactureProfessionnelle::query()
            ->where('patient_user_id', $user?->id)
            ->where('statut_paiement_patient', 'paye')
            ->with('serviceProfessionnel')
            ->latest('payee_le')
            ->get();

        $depensesMensuelles = $depensesPayees
            ->groupBy(fn (FactureProfessionnelle $facture) => optional($facture->payee_le ?? $facture->created_at)->format('Y-m'))
            ->map(fn (Collection $group) => (float) $group->sum('montant_total'));

        return view('patient.finances', [
            'dossierMedical' => $dossierMedical,
            'depensesPayees' => $depensesPayees,
            'depensesMensuelles' => $depensesMensuelles,
            'totalDepenses' => (float) $depensesPayees->sum('montant_total'),
            'depensesConsultations' => (float) $depensesPayees->where('type_facture', 'consultation')->sum('montant_total'),
            'depensesExamens' => (float) $depensesPayees->where('type_facture', 'examen')->sum('montant_total'),
        ]);
    }

    public function appointments(): View|RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous devez d\'abord creer votre dossier medical.');
        }

        $rendezVous = RendezVousProfessionnel::query()
            ->where('patient_user_id', $user?->id)
            ->with(['serviceProfessionnel', 'dossierProfessionnel.user', 'professionnel'])
            ->orderBy('date_proposee')
            ->get();

        return view('patient.appointments', [
            'dossierMedical' => $dossierMedical,
            'rendezVous' => $rendezVous,
            'calendarEvents' => $rendezVous->map(fn (RendezVousProfessionnel $rdv) => [
                'date' => optional($rdv->date_proposee)->format('Y-m-d'),
                'status' => $rdv->statut,
                'reference' => $rdv->reference,
                'service' => $rdv->serviceProfessionnel?->nom ?? 'Service',
            ])->values(),
        ]);
    }

    public function documents(): View|RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous devez d\'abord creer votre dossier medical.');
        }

        return view('patient.documents', [
            'dossierMedical' => $dossierMedical,
            'documents' => $this->buildDocumentsCollection($user?->id),
            'ordonnances' => OrdonnanceProfessionnelle::query()
                ->whereHas('consultation', fn ($query) => $query->where('patient_user_id', $user?->id))
                ->with(['consultation.rendezVous.serviceProfessionnel', 'professionnel'])
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }

    public function analyzeOrdonnance(OrdonnanceProfessionnelle $ordonnanceProfessionnelle, TreatmentSupportService $treatmentSupportService): RedirectResponse
    {
        $user = Auth::user();

        abort_if((int) ($ordonnanceProfessionnelle->consultation?->patient_user_id ?? 0) !== (int) $user?->id, 403);

        $analysis = $treatmentSupportService->analyzeForPatient($ordonnanceProfessionnelle);
        $user?->notify(new TraitementSuiviAnalyseNotification($ordonnanceProfessionnelle, $analysis));

        return redirect()->route('patient.documents.index')
            ->with('success', 'Analyse du traitement générée avec succès.')
            ->with('ordonnance_ai_analysis_id', $ordonnanceProfessionnelle->id)
            ->with('ordonnance_ai_analysis', $analysis);
    }

    public function alerts(): View|RedirectResponse
    {
        $user = Auth::user();
        $dossierMedical = $user?->dossierMedical;

        if (! $dossierMedical) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous devez d\'abord creer votre dossier medical.');
        }

        $upcomingAppointments = RendezVousProfessionnel::query()
            ->where('patient_user_id', $user?->id)
            ->whereIn('statut', ['en_attente', 'accepte'])
            ->whereBetween('date_proposee', [now(), now()->copy()->addDays(2)])
            ->count();

        $pendingInvoices = FactureProfessionnelle::query()
            ->where('patient_user_id', $user?->id)
            ->where('statut_paiement_patient', 'en_attente')
            ->count();

        $activeSubscription = $dossierMedical->activeSubscription()->first();
        $alerts = collect();

        if (filled($dossierMedical->allergies)) {
            $alerts->push([
                'niveau' => 'critical',
                'titre' => 'Allergies renseignees',
                'message' => (string) $dossierMedical->allergies,
            ]);
        }

        if (filled($dossierMedical->traitements_en_cours)) {
            $alerts->push([
                'niveau' => 'warning',
                'titre' => 'Traitements en cours',
                'message' => (string) $dossierMedical->traitements_en_cours,
            ]);
        }

        if ($activeSubscription && $activeSubscription->date_fin && $activeSubscription->date_fin->diffInDays(now()) <= 7) {
            $alerts->push([
                'niveau' => 'warning',
                'titre' => 'Abonnement proche expiration',
                'message' => 'Votre abonnement expire le '.$activeSubscription->date_fin->format('d/m/Y').'.',
            ]);
        }

        if ($upcomingAppointments > 0) {
            $alerts->push([
                'niveau' => 'info',
                'titre' => 'Rendez-vous proche',
                'message' => 'Vous avez '.$upcomingAppointments.' rendez-vous dans les 48 prochaines heures.',
            ]);
        }

        if ($pendingInvoices > 0) {
            $alerts->push([
                'niveau' => 'warning',
                'titre' => 'Paiement en attente',
                'message' => 'Vous avez '.$pendingInvoices.' facture(s) en attente de paiement.',
            ]);
        }

        return view('patient.alerts', [
            'dossierMedical' => $dossierMedical,
            'alerts' => $alerts,
            'notifications' => $user?->notifications()->latest()->paginate(20),
            'unreadCount' => $user?->unreadNotifications()->count() ?? 0,
        ]);
    }

    public function markNotificationAsRead(DatabaseNotification $notification): RedirectResponse
    {
        $user = Auth::user();

        abort_if((int) $notification->notifiable_id !== (int) $user?->id, 403);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marquee comme lue.');
    }

    public function markAllNotificationsAsRead(): RedirectResponse
    {
        $user = Auth::user();

        $user?->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont ete marquees comme lues.');
    }

    public function liveNotifications(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user?->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (DatabaseNotification $notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'information',
                'message' => $notification->data['message'] ?? 'Nouvelle notification',
                'data' => $notification->data,
                'created_at' => optional($notification->created_at)->toIso8601String(),
                'read_at' => optional($notification->read_at)->toIso8601String(),
            ])
            ->values();

        return response()->json([
            'unread_count' => $user?->unreadNotifications()->count() ?? 0,
            'notifications' => $notifications,
        ]);
    }

    private function buildDocumentsCollection(?int $patientUserId): Collection
    {
        if (! $patientUserId) {
            return collect();
        }

        $consultationDocuments = ConsultationDocument::query()
            ->whereHas('consultation', fn ($query) => $query->where('patient_user_id', $patientUserId))
            ->with('consultation')
            ->latest()
            ->get()
            ->map(fn (ConsultationDocument $document) => [
                'type' => 'document_consultation',
                'titre' => $document->nom_fichier,
                'path' => $document->file_path,
                'date' => $document->created_at,
            ]);

        $consultationResults = ConsultationProfessionnelle::query()
            ->where('patient_user_id', $patientUserId)
            ->whereNotNull('fichier_resultat_path')
            ->latest()
            ->get()
            ->map(fn (ConsultationProfessionnelle $consultation) => [
                'type' => 'resultat_consultation',
                'titre' => 'Resultat consultation #'.$consultation->id,
                'path' => $consultation->fichier_resultat_path,
                'date' => $consultation->updated_at,
            ]);

        $examResults = ExamenProfessionnel::query()
            ->where('patient_user_id', $patientUserId)
            ->whereNotNull('resultat_fichier_path')
            ->latest()
            ->get()
            ->map(fn (ExamenProfessionnel $examen) => [
                'type' => 'resultat_examen',
                'titre' => 'Resultat examen - '.$examen->libelle,
                'path' => $examen->resultat_fichier_path,
                'date' => $examen->updated_at,
            ]);

        $ordonnanceFiles = OrdonnanceProfessionnelle::query()
            ->whereHas('consultation', fn ($query) => $query->where('patient_user_id', $patientUserId))
            ->whereNotNull('fichier_joint_path')
            ->latest()
            ->get()
            ->map(fn (OrdonnanceProfessionnelle $ordonnance) => [
                'type' => 'ordonnance',
                'titre' => 'Ordonnance #'.$ordonnance->id,
                'path' => $ordonnance->fichier_joint_path,
                'date' => $ordonnance->updated_at,
            ]);

        return $consultationDocuments
            ->concat($consultationResults)
            ->concat($examResults)
            ->concat($ordonnanceFiles)
            ->sortByDesc('date')
            ->values();
    }

    private function resolveManagedMedicalDossier(?int $userId, ?string $reference): ?DossierMedical
    {
        if (! $userId) {
            return null;
        }

        $query = DossierMedical::query()->where('user_id', $userId);

        $reference = trim((string) $reference);
        if ($reference !== '') {
            return (clone $query)
                ->where(function ($inner) use ($reference): void {
                    $inner->where('numero_unique', $reference);

                    if (ctype_digit($reference)) {
                        $inner->orWhereKey((int) $reference);
                    }
                })
                ->first();
        }

        return $query->latest()->first();
    }
}
