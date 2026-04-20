<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDossierProfessionnelRequest;
use App\Http\Requests\UpdateDossierProfessionnelRequest;
use App\Models\DossierProfessionnel;
use App\Models\Frais;
use App\Models\SubscriptionProfessionnelle;
use App\Models\User;
use App\Notifications\ActivationDecisionNotification;
use App\Notifications\VerificationDouteNotification;
use App\Services\IdentityComplianceReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DossierProfessionnelController extends Controller
{
    public function index(Request $request): View
    {
        $query = DossierProfessionnel::query()
            ->with(['user', 'frais', 'encaissePar'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }

        $dossiers = $query->paginate(15)->withQueryString();

        $specialites = DossierProfessionnel::query()
            ->whereNotNull('specialite')
            ->where('specialite', '!=', '')
            ->select('specialite')
            ->distinct()
            ->orderBy('specialite')
            ->pluck('specialite');

        return view('dossier-professionnels.index', compact('dossiers', 'specialites'));
    }

    public function pendingValidation(): View
    {
        $dossiers = DossierProfessionnel::query()
            ->with(['user', 'frais'])
            ->where('statut', 'en_attente')
            ->latest()
            ->paginate(15);

        return view('dossier-professionnels.pending-validation', compact('dossiers'));
    }

    public function create(): View
    {
        $fraisInscription = Frais::where('type', 'inscription_pro')->get();

        return view('dossier-professionnels.create', compact('fraisInscription'));
    }

    public function createSelf(): View|RedirectResponse
    {
        $existingDossier = DossierProfessionnel::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($existingDossier) {
            if ($existingDossier->isValide()) {
                return redirect()->route('dashboard')->with('success', 'Votre profil professionnel est déjà validé.');
            }

            if ($existingDossier->statut_paiement_inscription !== 'paye') {
                return redirect()->route('user.professional.payment', $existingDossier)
                    ->with('success', 'Finalisez votre paiement pour continuer le traitement de votre dossier.');
            }

            return redirect()->route('user.professional.profile')
                ->with('success', 'Votre dossier est déjà soumis et en cours de vérification admin.');
        }

        $fraisInscription = Frais::where('type', 'inscription_pro')->get();

        return view('user.professional.create', [
            'fraisInscription' => $fraisInscription,
        ]);
    }

    public function store(StoreDossierProfessionnelRequest $request, IdentityComplianceReviewService $identityComplianceReviewService): RedirectResponse
    {
        return DB::transaction(function () use ($request, $identityComplianceReviewService) {
            $data = $request->validated();

            $review = $identityComplianceReviewService->reviewProfessionalSubmission([
                ...$data,
                'has_identity_image' => $request->hasFile('image_identite'),
            ]);

            if ((string) ($review['risk_level'] ?? 'low') === 'high') {
                return back()->with('error', 'Soumission refusee: verification professionnelle non conforme (doublon ou piece manquante).')
                    ->withInput();
            }

            if ($request->hasFile('image_identite')) {
                $data['image_identite_path'] = $request->file('image_identite')
                    ->store('dossiers-professionnels/identite', 'public');
            }

            if ($request->hasFile('attestation_professionnelle')) {
                $data['attestation_professionnelle_path'] = $request->file('attestation_professionnelle')
                    ->store('dossiers-professionnels/attestations', 'public');
            }

            if ($request->hasFile('document_prise_de_fonction')) {
                $data['document_prise_de_fonction_path'] = $request->file('document_prise_de_fonction')
                    ->store('dossiers-professionnels/documents', 'public');
            }

            unset($data['image_identite'], $data['attestation_professionnelle'], $data['document_prise_de_fonction']);

            $data['user_id'] = Auth::id();
            $data['statut'] = 'en_attente';
            $data['statut_paiement_inscription'] = $data['statut_paiement_inscription'] ?? 'en_attente';

            $dossier = DossierProfessionnel::create($data);

            if (in_array((string) ($review['risk_level'] ?? 'low'), ['medium', 'high'], true)) {
                User::query()
                    ->where('role', User::ROLE_ADMIN)
                    ->get()
                    ->each(fn (User $admin) => $admin->notify(new VerificationDouteNotification(
                        scope: 'dossier_professionnel',
                        recordId: (int) $dossier->id,
                        riskLevel: (string) ($review['risk_level'] ?? 'medium'),
                        reasons: array_values((array) ($review['reasons'] ?? [])),
                        message: 'Doute de conformite sur un dossier professionnel a verifier.',
                    )));
            }

            return redirect()->route('dossier-professionnels.index')
                ->with('success', 'Votre dossier professionnel a été soumis. Un administrateur va le valider.');
        });
    }

    public function storeSelf(StoreDossierProfessionnelRequest $request, IdentityComplianceReviewService $identityComplianceReviewService): RedirectResponse
    {
        return DB::transaction(function () use ($request, $identityComplianceReviewService) {
            $data = $request->validated();

            $review = $identityComplianceReviewService->reviewProfessionalSubmission([
                ...$data,
                'has_identity_image' => $request->hasFile('image_identite'),
            ]);

            if ((string) ($review['risk_level'] ?? 'low') === 'high') {
                return back()->with('error', 'Soumission refusee: verification professionnelle non conforme (doublon ou piece manquante).')
                    ->withInput();
            }

            if ($request->hasFile('image_identite')) {
                $data['image_identite_path'] = $request->file('image_identite')
                    ->store('dossiers-professionnels/identite', 'public');
            }

            if ($request->hasFile('attestation_professionnelle')) {
                $data['attestation_professionnelle_path'] = $request->file('attestation_professionnelle')
                    ->store('dossiers-professionnels/attestations', 'public');
            }

            if ($request->hasFile('document_prise_de_fonction')) {
                $data['document_prise_de_fonction_path'] = $request->file('document_prise_de_fonction')
                    ->store('dossiers-professionnels/documents', 'public');
            }

            unset($data['image_identite'], $data['attestation_professionnelle'], $data['document_prise_de_fonction']);

            $data['user_id'] = Auth::id();
            $data['statut'] = 'en_attente';
            $data['statut_paiement_inscription'] = 'en_attente';
            $data['mode_paiement_inscription'] = null;
            $data['reference_paiement_inscription'] = null;

            $dossier = DossierProfessionnel::create($data);

            if (in_array((string) ($review['risk_level'] ?? 'low'), ['medium', 'high'], true)) {
                User::query()
                    ->where('role', User::ROLE_ADMIN)
                    ->get()
                    ->each(fn (User $admin) => $admin->notify(new VerificationDouteNotification(
                        scope: 'dossier_professionnel',
                        recordId: (int) $dossier->id,
                        riskLevel: (string) ($review['risk_level'] ?? 'medium'),
                        reasons: array_values((array) ($review['reasons'] ?? [])),
                        message: 'Doute de conformite sur un dossier professionnel a verifier.',
                    )));
            }

            return redirect()->route('user.professional.payment', $dossier)
                ->with('success', 'Dossier professionnel créé. Finalisez votre paiement d\'inscription.');
        });
    }

    public function paymentForm(DossierProfessionnel $dossierProfessionnel): View
    {
        abort_if($dossierProfessionnel->user_id !== Auth::id(), 403);

        return view('user.professional.payment', [
            'dossierProfessionnel' => $dossierProfessionnel->load('frais', 'user'),
        ]);
    }

    public function professionalProfile(): View|RedirectResponse
    {
        $dossier = DossierProfessionnel::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if (! $dossier) {
            return redirect()->route('user.professional.create')
                ->with('error', 'Vous n\'avez pas encore de dossier professionnel.');
        }

        return view('user.professional.profile', [
            'dossierProfessionnel' => $dossier,
        ]);
    }

    public function processPayment(Request $request, DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        abort_if($dossierProfessionnel->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'payment_channel' => ['required', 'in:mtn,airtel,visa'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'card_holder_name' => ['nullable', 'string', 'max:255'],
            'card_number' => ['nullable', 'string', 'max:25'],
            'card_expiry_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'card_expiry_year' => ['nullable', 'integer', 'min:'.now()->year, 'max:'.(now()->year + 20)],
            'card_cvv' => ['nullable', 'digits_between:3,4'],
        ]);

        if ($validated['payment_channel'] === 'visa') {
            $request->validate([
                'card_holder_name' => ['required', 'string', 'max:255'],
                'card_number' => ['required', 'string', 'regex:/^[0-9\s]{13,19}$/'],
                'card_expiry_month' => ['required', 'integer', 'min:1', 'max:12'],
                'card_expiry_year' => ['required', 'integer', 'min:'.now()->year, 'max:'.(now()->year + 20)],
                'card_cvv' => ['required', 'digits_between:3,4'],
            ]);
        }

        $modePaiement = $validated['payment_channel'] === 'visa' ? 'carte' : 'mobile_money';
        $provider = strtoupper($validated['payment_channel']);
        $reference = 'PRO-PAY-'.now()->format('YmdHis').'-'.strtoupper(substr($provider, 0, 3)).'-'.mt_rand(100, 999);

        $dossierProfessionnel->update([
            'statut' => 'en_attente',
            'mode_paiement_inscription' => $modePaiement,
            'reference_paiement_inscription' => $reference,
            'statut_paiement_inscription' => 'paye',
            'notes' => trim(($dossierProfessionnel->notes ?? '').' '.(
                $provider === 'VISA'
                    ? 'Paiement self-service via VISA - Titulaire: '.$request->card_holder_name.' - Carte: **** **** **** '.substr(preg_replace('/\D+/', '', (string) $request->card_number), -4)
                    : 'Paiement self-service via '.$provider.($request->filled('phone_number') ? ' ('.$request->phone_number.')' : '')
            )),
            'encaisse_par_user_id' => Auth::id(),
            'encaisse_le' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Paiement reçu. Votre dossier professionnel est en attente de validation admin.');
    }

    public function show(DossierProfessionnel $dossierProfessionnel): View
    {
        $dossierProfessionnel->load(['user', 'frais', 'encaissePar', 'services', 'subscriptions.frais']);

        return view('dossier-professionnels.show', compact('dossierProfessionnel'));
    }

    public function verification(DossierProfessionnel $dossierProfessionnel, IdentityComplianceReviewService $identityComplianceReviewService): View
    {
        $dossierProfessionnel->load(['user', 'frais']);

        $complianceReview = $identityComplianceReviewService->reviewProfessionalDossier($dossierProfessionnel);

        return view('dossier-professionnels.verification', compact('dossierProfessionnel', 'complianceReview'));
    }

    public function edit(DossierProfessionnel $dossierProfessionnel): View
    {
        return view('dossier-professionnels.edit', compact('dossierProfessionnel'));
    }

    public function update(UpdateDossierProfessionnelRequest $request, DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_identite')) {
            $data['image_identite_path'] = $request->file('image_identite')
                ->store('dossiers-professionnels/identite', 'public');
        }

        if ($request->hasFile('attestation_professionnelle')) {
            $data['attestation_professionnelle_path'] = $request->file('attestation_professionnelle')
                ->store('dossiers-professionnels/attestations', 'public');
        }

        if ($request->hasFile('document_prise_de_fonction')) {
            $data['document_prise_de_fonction_path'] = $request->file('document_prise_de_fonction')
                ->store('dossiers-professionnels/documents', 'public');
        }

        unset($data['image_identite'], $data['attestation_professionnelle'], $data['document_prise_de_fonction']);

        $dossierProfessionnel->update($data);

        return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
            ->with('success', 'Dossier mis à jour avec succès.');
    }

    public function destroy(DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        $dossierProfessionnel->delete();

        return redirect()->route('dossier-professionnels.index')
            ->with('success', 'Dossier supprimé.');
    }

    public function valider(DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        if (! $dossierProfessionnel->isEnAttente()) {
            return back()->with('error', 'Ce dossier ne peut pas être validé.');
        }

        return DB::transaction(function () use ($dossierProfessionnel) {
            $numeroLicence = 'PRO-'.now()->year.'-'.strtoupper(Str::random(6));

            while (DossierProfessionnel::where('numero_licence', $numeroLicence)->exists()) {
                $numeroLicence = 'PRO-'.now()->year.'-'.strtoupper(Str::random(6));
            }

            $dossierProfessionnel->update([
                'statut' => 'valide',
                'numero_licence' => $numeroLicence,
                'encaisse_par_user_id' => Auth::id(),
                'encaisse_le' => now(),
            ]);

            if ($dossierProfessionnel->user) {
                $dossierProfessionnel->user->update(['role' => 'professional']);

                $dossierProfessionnel->user->notify(new ActivationDecisionNotification(
                    profileType: 'professionnel',
                    decision: 'valide',
                    message: 'Votre profil professionnel est active. Vous pouvez acceder a votre espace de travail.',
                    dossierReference: (string) ($dossierProfessionnel->numero_licence ?? $dossierProfessionnel->id),
                    note: null,
                    actionUrl: route('user.validation.status'),
                ));
            }

            $fraisReabonnement = Frais::where('type', 'reabonnement_pro')->first();

            if ($fraisReabonnement && ! $dossierProfessionnel->subscriptions()->exists()) {
                SubscriptionProfessionnelle::createWithAutoDate(
                    $dossierProfessionnel->id,
                    $fraisReabonnement->id,
                    1,
                    null,
                    'exonere',
                    Auth::id()
                );
            }

            return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
                ->with('success', 'Dossier validé. La licence '.$numeroLicence.' a été attribuée.');
        });
    }

    public function recaler(Request $request, DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:1000']]);

        $dossierProfessionnel->update([
            'statut' => 'recale',
            'notes' => $request->notes,
        ]);

        if ($dossierProfessionnel->user) {
            $dossierProfessionnel->user->notify(new ActivationDecisionNotification(
                profileType: 'professionnel',
                decision: 'rejete',
                message: 'Votre profil professionnel n a pas ete active apres verification.',
                dossierReference: (string) ($dossierProfessionnel->numero_licence ?? $dossierProfessionnel->id),
                note: $request->notes,
                actionUrl: route('user.validation.status'),
            ));
        }

        return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
            ->with('success', 'Dossier recalé.');
    }

    public function remettre(DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        $dossierProfessionnel->update(['statut' => 'en_attente']);

        return back()->with('success', 'Dossier remis en attente.');
    }
}
