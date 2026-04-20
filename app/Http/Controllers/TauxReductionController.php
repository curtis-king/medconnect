<?php

namespace App\Http\Controllers;

use App\Models\TauxReduction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TauxReductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tauxReductions = TauxReduction::latest()->paginate(10);
        $types = TauxReduction::TYPES;

        return view('taux-reductions.index', compact('tauxReductions', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $types = TauxReduction::TYPES;

        return view('taux-reductions.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'taux' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inscription,reabonnement,contribution,special',
            'detail' => 'nullable|string',
            'actif' => 'boolean',
        ]);

        TauxReduction::create($validated);

        return redirect()->route('taux-reductions.index')
            ->with('success', 'Taux de réduction créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TauxReduction $tauxReduction): View
    {
        $types = TauxReduction::TYPES;

        return view('taux-reductions.show', compact('tauxReduction', 'types'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TauxReduction $tauxReduction): View
    {
        $types = TauxReduction::TYPES;

        return view('taux-reductions.edit', compact('tauxReduction', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TauxReduction $tauxReduction): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'taux' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inscription,reabonnement,contribution,special',
            'detail' => 'nullable|string',
            'actif' => 'boolean',
        ]);

        $tauxReduction->update($validated);

        return redirect()->route('taux-reductions.index')
            ->with('success', 'Taux de réduction mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TauxReduction $tauxReduction): RedirectResponse
    {
        $tauxReduction->delete();

        return redirect()->route('taux-reductions.index')
            ->with('success', 'Taux de réduction supprimé avec succès.');
    }
}
