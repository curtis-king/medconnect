<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDossierMedicalRequest;
use App\Http\Requests\UpdateDossierMedicalRequest;
use App\Models\DossierMedical;
use App\Models\Frais;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\VerificationDouteNotification;
use App\Services\IdentityComplianceReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DossierMedicalController extends Controller
{
    public function createSelf(): View|RedirectResponse
    {
        $fraisInscriptions = Frais::query()->where('type', 'inscription')->get();
        $modeDeclaration = request()->query('mode') === 'dependant' ? 'dependant' : 'personnel';

        if ($modeDeclaration === 'personnel' && DossierMedical::query()
            ->where('user_id', Auth::id())
            ->where('est_personne_a_charge', false)
            ->exists()) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre dossier medical personnel existe deja. Utilisez uniquement le mode personne a charge pour un nouveau dossier.');
        }

        return view('user.adherer.create', [
            'fraisInscriptions' => $fraisInscriptions,
            'modeDeclaration' => $modeDeclaration,
        ]);
    }

    public function storeSelf(StoreDossierMedicalRequest $request, IdentityComplianceReviewService $identityComplianceReviewService): RedirectResponse
    {
        $data = $request->validated();

        $declarationMode = (string) ($data['declaration_mode'] ?? 'personnel');

        if (
            $declarationMode === 'personnel'
            && DossierMedical::query()
                ->where('user_id', Auth::id())
                ->where('est_personne_a_charge', false)
                ->exists()
        ) {
            return back()->with('error', 'Creation refusee: un seul dossier personnel est autorise par compte.')
                ->withInput();
        }

        $review = $identityComplianceReviewService->reviewMedicalSubmission($data);

        if ((string) ($review['risk_level'] ?? 'low') === 'high') {
            return back()->with('error', 'Creation refusee: verification identite non conforme (doublon ou piece obligatoire manquante).')
                ->withInput();
        }

        $data['est_personne_a_charge'] = $declarationMode === 'dependant';
        $data['lien_avec_responsable'] = $declarationMode === 'dependant'
            ? ($data['lien_avec_responsable'] ?? null)
            : null;
        unset($data['declaration_mode']);

        $data['numero_unique'] = 'DM-'.date('Y').'-'.str_pad(DossierMedical::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['source_creation'] = 'en_ligne';
        $data['user_id'] = Auth::id();
        $data['statut_paiement_inscription'] = 'en_attente';
        $data['mode_paiement_inscription'] = null;
        $data['reference_paiement_inscription'] = null;

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil_path'] = $request->file('photo_profil')->store('photos-profils', 'public');
        }

        if ($request->hasFile('piece_identite_recto')) {
            $data['piece_identite_recto_path'] = $request->file('piece_identite_recto')->store('pieces-identite', 'public');
        }

        if ($request->hasFile('piece_identite_verso')) {
            $data['piece_identite_verso_path'] = $request->file('piece_identite_verso')->store('pieces-identite', 'public');
        }

        $dossier = DossierMedical::create($data);

        if (in_array((string) ($review['risk_level'] ?? 'low'), ['medium', 'high'], true)) {
            User::query()
                ->where('role', User::ROLE_ADMIN)
                ->get()
                ->each(fn (User $admin) => $admin->notify(new VerificationDouteNotification(
                    scope: 'dossier_medical',
                    recordId: (int) $dossier->id,
                    riskLevel: (string) ($review['risk_level'] ?? 'medium'),
                    reasons: array_values((array) ($review['reasons'] ?? [])),
                    message: 'Doute de conformite sur un dossier medical a verifier par l administration.',
                )));
        }

        if (! empty($data['frais_id'])) {
            $frais = Frais::find($data['frais_id']);

            if ($frais) {
                Paiement::create([
                    'dossier_medical_id' => $dossier->id,
                    'frais_inscription_id' => $frais->id,
                    'type_paiement' => 'inscription',
                    'montant' => $frais->prix,
                    'periode_debut' => now(),
                    'periode_fin' => now()->addYear(),
                    'nombre_mois' => 12,
                    'statut' => 'en_attente',
                    'date_echeance' => now()->addHours(2),
                ]);
            }
        }

        return redirect()->route('user.adherer.payment', $dossier)
            ->with('success', 'Dossier créé. Finalisez maintenant votre paiement d\'inscription.');
    }

    public function paymentForm(DossierMedical $dossierMedical): View
    {
        abort_if($dossierMedical->user_id !== Auth::id(), 403);

        return view('user.adherer.payment', [
            'dossier' => $dossierMedical->load('frais'),
        ]);
    }

    public function processPayment(Request $request, DossierMedical $dossierMedical): RedirectResponse
    {
        abort_if($dossierMedical->user_id !== Auth::id(), 403);

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
        $reference = 'PAY-'.now()->format('YmdHis').'-'.strtoupper(substr($provider, 0, 3)).'-'.mt_rand(100, 999);

        $paiement = Paiement::query()
            ->where('dossier_medical_id', $dossierMedical->id)
            ->where('type_paiement', 'inscription')
            ->latest()
            ->first();

        if (! $paiement) {
            $frais = $dossierMedical->frais;

            if (! $frais) {
                return back()->with('error', 'Aucun frais d\'inscription associé au dossier.');
            }

            $paiement = Paiement::create([
                'dossier_medical_id' => $dossierMedical->id,
                'frais_inscription_id' => $frais->id,
                'type_paiement' => 'inscription',
                'montant' => $frais->prix,
                'periode_debut' => now(),
                'periode_fin' => now()->addYear(),
                'nombre_mois' => 12,
                'statut' => 'en_attente',
            ]);
        }

        $paiement->update([
            'statut' => 'paye',
            'mode_paiement' => $modePaiement,
            'reference_paiement' => $reference,
            'notes' => $provider === 'VISA'
                ? 'Paiement self-service via VISA - Titulaire: '.$request->card_holder_name.' - Carte: **** **** **** '.substr(preg_replace('/\D+/', '', (string) $request->card_number), -4)
                : 'Paiement self-service via '.$provider.($request->filled('phone_number') ? ' ('.$request->phone_number.')' : ''),
            'encaisse_par_user_id' => Auth::id(),
            'date_encaissement' => now(),
            'date_echeance' => null,
        ]);

        $dossierMedical->update([
            'actif' => true,
            'statut_paiement_inscription' => 'paye',
            'mode_paiement_inscription' => $modePaiement,
            'reference_paiement_inscription' => $reference,
        ]);

        $this->createFirstSubscription($dossierMedical, $modePaiement);

        return redirect()->route('dashboard')
            ->with('success', 'Paiement confirmé. Votre dossier médical est maintenant actif.');
    }

    public function medicalProfile(): View|RedirectResponse
    {
        $dossier = DossierMedical::query()->where('user_id', Auth::id())->latest()->first();

        if (! $dossier) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous n\'avez pas encore de dossier médical.');
        }

        return view('user.adherer.profile', [
            'dossier' => $dossier,
        ]);
    }

    public function updateMedicalProfile(UpdateDossierMedicalRequest $request): RedirectResponse
    {
        $dossier = DossierMedical::query()->where('user_id', Auth::id())->latest()->first();

        if (! $dossier) {
            return redirect()->route('user.adherer.create')
                ->with('error', 'Vous n\'avez pas encore de dossier médical.');
        }

        $data = $request->validated();

        if (! $dossier->photo_profil_path && ! $request->hasFile('photo_profil')) {
            return back()
                ->withErrors(['photo_profil' => 'La photo de profil est obligatoire.'])
                ->withInput();
        }

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil_path'] = $request->file('photo_profil')->store('photos-profils', 'public');
        }

        if ($request->hasFile('piece_identite_recto')) {
            $data['piece_identite_recto_path'] = $request->file('piece_identite_recto')->store('pieces-identite', 'public');
        }

        if ($request->hasFile('piece_identite_verso')) {
            $data['piece_identite_verso_path'] = $request->file('piece_identite_verso')->store('pieces-identite', 'public');
        }

        $dossier->update($data);

        return redirect()->route('user.medical-profile.edit')
            ->with('success', 'Profil médical mis à jour avec succès.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dossiers = DossierMedical::query()
            ->with(['user', 'frais', 'encaissePar'])
            ->actif()
            ->latest()
            ->paginate(15);

        return view('dossiers-medicaux.index', compact('dossiers'));
    }

    public function pendingValidation(): View
    {
        $dossiers = DossierMedical::query()
            ->with(['user'])
            ->where(function ($query): void {
                $query->where('statut_paiement_inscription', '!=', 'paye')
                    ->orWhere('documents_validation_statut', 'en_attente')
                    ->orWhere('actif', false);
            })
            ->latest()
            ->paginate(15);

        return view('dossiers-medicaux.pending-validation', compact('dossiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fraisInscriptions = Frais::query()->where('type', 'inscription')->get();

        return view('dossiers-medicaux.create', compact('fraisInscriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDossierMedicalRequest $request)
    {
        $data = $request->validated();

        // Générer un numéro unique
        $data['numero_unique'] = 'DM-'.date('Y').'-'.str_pad(DossierMedical::count() + 1, 4, '0', STR_PAD_LEFT);

        // Définir la source de création
        $data['source_creation'] = $data['source_creation'] ?? 'guichet';

        // Générer automatiquement la référence de paiement si elle n'est pas fournie
        if (empty($data['reference_paiement_inscription'])) {
            $timestamp = now()->timestamp;
            $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
            $data['reference_paiement_inscription'] = 'PAY-'.$timestamp.'-'.$random;
        }

        // Gérer les fichiers uploadés
        if ($request->hasFile('photo_profil')) {
            $data['photo_profil_path'] = $request->file('photo_profil')->store('photos-profils', 'public');
        }

        if ($request->hasFile('piece_identite_recto')) {
            $data['piece_identite_recto_path'] = $request->file('piece_identite_recto')->store('pieces-identite', 'public');
        }

        if ($request->hasFile('piece_identite_verso')) {
            $data['piece_identite_verso_path'] = $request->file('piece_identite_verso')->store('pieces-identite', 'public');
        }

        // Créer le dossier médical
        $dossier = DossierMedical::create($data);

        // Créer automatiquement le paiement d'inscription si un frais est sélectionné
        if (! empty($data['frais_id'])) {
            $frais = Frais::find($data['frais_id']);

            if ($frais) {
                // Calculer la période (par défaut 1 an pour l'inscription)
                $periodeDebut = now();
                $periodeFin = now()->addYear();

                \App\Models\Paiement::create([
                    'dossier_medical_id' => $dossier->id,
                    'frais_inscription_id' => $frais->id,
                    'type_paiement' => 'inscription',
                    'montant' => $frais->prix,
                    'periode_debut' => $periodeDebut,
                    'periode_fin' => $periodeFin,
                    'nombre_mois' => 12,
                    'statut' => $data['statut_paiement_inscription'] ?? 'en_attente',
                    'mode_paiement' => $data['mode_paiement_inscription'] ?? null,
                    'reference_paiement' => $data['reference_paiement_inscription'],
                    'encaisse_par_user_id' => Auth::id(),
                    'date_encaissement' => (($data['statut_paiement_inscription'] ?? 'en_attente') === 'paye') ? now() : null,
                ]);

                // Mettre à jour le statut du dossier selon le paiement
                if (($data['statut_paiement_inscription'] ?? 'en_attente') === 'paye') {
                    $dossier->update(['actif' => true]);

                    // Créer automatiquement la première subscription
                    $this->createFirstSubscription($dossier, $data['mode_paiement_inscription'] ?? null);
                }
            }
        }

        return redirect()
            ->route('dossier-medicals.show', $dossier->id)
            ->with('success', 'Dossier médical créé avec succès.');
    }

    /**
     * Create the first subscription when registration payment is confirmed.
     */
    private function createFirstSubscription(DossierMedical $dossier, ?string $modePaiement = null): void
    {
        // Chercher un frais de type réabonnement
        $fraisReabonnement = Frais::where('type', 'reabonnement')->first();

        // Si pas de frais de réabonnement trouvé, utiliser le frais d'inscription
        if (! $fraisReabonnement) {
            $fraisReabonnement = Frais::find($dossier->frais_id);
        }

        if (! $fraisReabonnement) {
            return;
        }

        // Vérifier si une subscription existe déjà
        if ($dossier->subscriptions()->exists()) {
            return;
        }

        // Créer la première subscription (1 mois inclus avec l'inscription)
        Subscription::create([
            'dossier_medical_id' => $dossier->id,
            'frais_id' => $fraisReabonnement->id,
            'date_debut' => now(),
            'date_fin' => now()->addMonth()->subDay(),
            'nombre_mois' => 1,
            'montant' => 0, // Premier mois gratuit avec l'inscription
            'statut' => 'actif',
            'mode_paiement' => $modePaiement,
            'encaisse_par_user_id' => Auth::id(),
            'date_paiement' => now(),
            'notes' => 'Premier mois d\'abonnement inclus avec l\'inscription',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(DossierMedical $dossierMedical)
    {
        return view('dossiers-medicaux.show', [
            'dossier' => $dossierMedical->load(['user', 'frais', 'encaissePar']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DossierMedical $dossierMedical)
    {
        $fraisInscriptions = Frais::query()->where('type', 'inscription')->get();

        return view('dossiers-medicaux.edit', [
            'dossier' => $dossierMedical->load(['user', 'frais', 'encaissePar']),
            'fraisInscriptions' => $fraisInscriptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDossierMedicalRequest $request, DossierMedical $dossierMedical)
    {
        $data = $request->validated();

        $dossierMedical->update($data);

        return redirect()
            ->route('dossier-medicals.show', $dossierMedical->id)
            ->with('success', 'Dossier médical mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DossierMedical $dossierMedical)
    {
        $dossierMedical->delete();

        return redirect()
            ->route('dossier-medicals.index')
            ->with('success', 'Dossier médical supprimé avec succès.');
    }

    /**
     * Display the medical card index page.
     */
    public function carteIndex()
    {
        return view('carte-medicale.index');
    }

    /**
     * Search for dossiers for medical card.
     */
    public function carteSearch()
    {
        $query = request('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $dossiers = DossierMedical::query()
            ->where('actif', true)
            ->where(function ($q) use ($query) {
                $q->where('numero_unique', 'like', '%'.$query.'%')
                    ->orWhere('nom', 'like', '%'.$query.'%')
                    ->orWhere('prenom', 'like', '%'.$query.'%')
                    ->orWhere('telephone', 'like', '%'.$query.'%');
            })
            ->limit(10)
            ->get(['id', 'numero_unique', 'nom', 'prenom', 'telephone', 'photo_profil_path', 'groupe_sanguin', 'date_naissance']);

        return response()->json($dossiers);
    }

    /**
     * Display the medical card request page.
     */
    public function carteDemande(DossierMedical $dossierMedical)
    {
        return view('carte-medicale.demande', [
            'dossier' => $dossierMedical->load(['frais', 'activeSubscription']),
        ]);
    }

    /**
     * Display the medical card generation page (PVC format).
     */
    public function carteGenerer(DossierMedical $dossierMedical)
    {
        return view('carte-medicale.generer', [
            'dossier' => $dossierMedical->load(['frais', 'activeSubscription']),
        ]);
    }

    /**
     * Display the printable medical card page (optimized for PVC printing).
     */
    public function carteImprimer(DossierMedical $dossierMedical)
    {
        return view('carte-medicale.imprimer', [
            'dossier' => $dossierMedical->load(['frais', 'activeSubscription']),
        ]);
    }

    /**
     * Public route to scan medical card QR code and view medical info if sharing is enabled.
     */
    public function carteScan(string $code)
    {
        $dossier = DossierMedical::where('code_partage', $code)
            ->orWhere('numero_unique', $code)
            ->first();

        if (! $dossier) {
            return view('carte-medicale.scan', [
                'error' => 'Carte médicale introuvable.',
                'dossier' => null,
            ]);
        }

        if (! $dossier->partage_actif) {
            return view('carte-medicale.scan', [
                'error' => 'Le partage des informations médicales n\'est pas activé pour ce dossier.',
                'dossier' => null,
                'partage_desactive' => true,
            ]);
        }

        return view('carte-medicale.scan', [
            'dossier' => $dossier->load(['activeSubscription']),
            'error' => null,
        ]);
    }
}
