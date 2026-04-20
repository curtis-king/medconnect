<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRendezVousRequest;
use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use App\Models\RendezVousProfessionnel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RendezVousController extends Controller
{
    public function index(Request $request): View
    {
        $specialites = DossierProfessionnel::query()
            ->valide()
            ->whereHas('user', fn ($q) => $q->active())
            ->whereNotNull('specialite')
            ->pluck('specialite')
            ->unique()
            ->sort()
            ->values();

        $villes = User::query()
            ->active()
            ->whereHas('dossierProfessionnel', fn ($q) => $q->valide())
            ->whereNotNull('city')
            ->pluck('city')
            ->unique()
            ->sort()
            ->values();

        $quartiers = User::query()
            ->active()
            ->whereHas('dossierProfessionnel', fn ($q) => $q->valide())
            ->whereNotNull('quartier')
            ->pluck('quartier')
            ->unique()
            ->sort()
            ->values();

        $professionnels = DossierProfessionnel::query()
            ->valide()
            ->with(['user', 'servicesActifs'])
            ->when(Auth::check(), fn ($q) => $q->where('user_id', '!=', Auth::id()))
            ->whereHas('user', function ($q) use ($request) {
                $q->active();
                if ($request->filled('ville')) {
                    $q->where('city', $request->ville);
                }
                if ($request->filled('quartier')) {
                    $q->where('quartier', $request->quartier);
                }
            })
            ->when($request->filled('specialite'), fn ($q) => $q->where('specialite', $request->specialite))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->search.'%';
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', $search));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $managedMedicalDossiers = DossierMedical::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('rendez-vous.index', compact('professionnels', 'specialites', 'villes', 'quartiers', 'managedMedicalDossiers'));
    }

    public function store(StoreRendezVousRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $dossierPro = DossierProfessionnel::query()
            ->valide()
            ->with('user')
            ->findOrFail($validated['dossier_professionnel_id']);

        if ((int) $dossierPro->user_id === (int) Auth::id()) {
            return back()
                ->withErrors(['dossier_professionnel_id' => 'Vous ne pouvez pas prendre rendez-vous avec vous-meme.'])
                ->withInput();
        }

        $dateProposee = Carbon::parse($validated['date_proposee_jour'].' '.$validated['heure_proposee']);

        if (! empty($validated['service_professionnel_id'])) {
            $serviceBelongsToProfessional = $dossierPro->servicesActifs()
                ->whereKey($validated['service_professionnel_id'])
                ->exists();

            if (! $serviceBelongsToProfessional) {
                return back()
                    ->withErrors(['service_professionnel_id' => 'Le service sélectionné ne correspond pas au professionnel choisi.'])
                    ->withInput();
            }
        }

        $targetDossierMedical = null;

        if (! empty($validated['dossier_medical_id'])) {
            $targetDossierMedical = DossierMedical::query()
                ->where('user_id', Auth::id())
                ->whereKey($validated['dossier_medical_id'])
                ->first();
        }

        if (! $targetDossierMedical && ! empty($validated['patient_dossier_reference'])) {
            $reference = trim((string) $validated['patient_dossier_reference']);

            $targetDossierMedical = DossierMedical::query()
                ->where('user_id', Auth::id())
                ->where(function ($query) use ($reference): void {
                    $query->where('numero_unique', $reference);

                    if (ctype_digit($reference)) {
                        $query->orWhereKey((int) $reference);
                    }
                })
                ->first();
        }

        if (! $targetDossierMedical) {
            $targetDossierMedical = Auth::user()?->dossierMedical;
        }

        if (! $targetDossierMedical) {
            return back()
                ->withErrors(['patient_dossier_reference' => 'Aucun dossier medical valide trouve. Selectionnez un dossier enfant/personne a charge ou renseignez un numero dossier valide.'])
                ->withInput();
        }

        RendezVousProfessionnel::create([
            'dossier_professionnel_id' => $dossierPro->id,
            'professionnel_user_id' => $dossierPro->user_id,
            'service_professionnel_id' => $validated['service_professionnel_id'] ?? null,
            'patient_user_id' => Auth::id(),
            'dossier_medical_id' => $targetDossierMedical->id,
            'numero_dossier_reference' => $targetDossierMedical->numero_unique,
            'reference' => 'RDV-'.strtoupper(Str::random(8)),
            'type_demande' => 'consultation',
            'type_rendez_vous' => 'consultation',
            'mode_deroulement' => $validated['mode_deroulement'],
            'statut' => 'en_attente',
            'statut_acceptation' => 'en_attente',
            'date_proposee' => $dateProposee,
            'date_proposee_jour' => $validated['date_proposee_jour'],
            'heure_proposee' => $validated['heure_proposee'],
            'motif' => $validated['motif'],
        ]);

        $relationLabel = $targetDossierMedical->est_personne_a_charge
            ? ($targetDossierMedical->lien_avec_responsable_label ?? ucfirst((string) $targetDossierMedical->lien_avec_responsable))
            : 'Dossier personnel';

        return redirect()->route('rendez-vous.index')
            ->with('success', 'Votre demande de rendez-vous a bien ete soumise pour '.$targetDossierMedical->nom_complet.' ('.$targetDossierMedical->numero_unique.' - '.$relationLabel.'). Le professionnel vous repondra rapidement.');
    }
}
