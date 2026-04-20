<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceProfessionnelRequest;
use App\Models\DossierProfessionnel;
use App\Models\ServiceProfessionnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceProfessionnelController extends Controller
{
    private function authorizeAccess(DossierProfessionnel $dossierProfessionnel): void
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $dossierProfessionnel->user_id !== $user->id) {
            abort(403);
        }
    }

    public function index(DossierProfessionnel $dossierProfessionnel): View
    {
        $this->authorizeAccess($dossierProfessionnel);
        $services = $dossierProfessionnel->services()->latest()->paginate(20);

        return view('services-professionnels.index', compact('dossierProfessionnel', 'services'));
    }

    public function create(DossierProfessionnel $dossierProfessionnel): View
    {
        $this->authorizeAccess($dossierProfessionnel);

        return view('services-professionnels.create', compact('dossierProfessionnel'));
    }

    public function store(StoreServiceProfessionnelRequest $request, DossierProfessionnel $dossierProfessionnel): RedirectResponse
    {
        $this->authorizeAccess($dossierProfessionnel);
        $dossierProfessionnel->services()->create($request->validated());

        if ($request->input('_from') === 'workspace') {
            return redirect()->route('professional.workspace.dashboard')
                ->with('success', 'Service ajouté avec succès.');
        }

        return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
            ->with('success', 'Service ajouté avec succès.');
    }

    public function show(DossierProfessionnel $dossierProfessionnel, ServiceProfessionnel $service): View
    {
        $this->authorizeAccess($dossierProfessionnel);

        return view('services-professionnels.show', compact('dossierProfessionnel', 'service'));
    }

    public function edit(DossierProfessionnel $dossierProfessionnel, ServiceProfessionnel $service): View
    {
        $this->authorizeAccess($dossierProfessionnel);

        return view('services-professionnels.edit', compact('dossierProfessionnel', 'service'));
    }

    public function update(StoreServiceProfessionnelRequest $request, DossierProfessionnel $dossierProfessionnel, ServiceProfessionnel $service): RedirectResponse
    {
        $this->authorizeAccess($dossierProfessionnel);
        $service->update($request->validated());

        return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
            ->with('success', 'Service mis à jour.');
    }

    public function destroy(DossierProfessionnel $dossierProfessionnel, ServiceProfessionnel $service): RedirectResponse
    {
        $this->authorizeAccess($dossierProfessionnel);
        $service->delete();

        return redirect()->route('dossier-professionnels.show', $dossierProfessionnel)
            ->with('success', 'Service supprimé.');
    }

    public function toggleActif(DossierProfessionnel $dossierProfessionnel, ServiceProfessionnel $service): RedirectResponse
    {
        $this->authorizeAccess($dossierProfessionnel);
        $service->update(['actif' => ! $service->actif]);

        return back()->with('success', 'Statut du service mis à jour.');
    }
}
