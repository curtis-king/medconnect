<?php

namespace App\Http\Controllers;

use App\Models\FraisInscription;
use Illuminate\Http\Request;

class FraisInscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fraisInscriptions = FraisInscription::all();

        return view('frais-inscriptions.index', compact('fraisInscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('frais-inscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'detail' => 'nullable|string',
        ]);

        FraisInscription::create($request->all());

        return redirect()->route('frais-inscriptions.index')
            ->with('success', 'Frais d\'inscription créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FraisInscription $fraisInscription)
    {
        return view('frais-inscriptions.show', compact('fraisInscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FraisInscription $fraisInscription)
    {
        return view('frais-inscriptions.edit', compact('fraisInscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FraisInscription $fraisInscription)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'detail' => 'nullable|string',
        ]);

        $fraisInscription->update($request->all());

        return redirect()->route('frais-inscriptions.index')
            ->with('success', 'Frais d\'inscription mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FraisInscription $fraisInscription)
    {
        $fraisInscription->delete();

        return redirect()->route('frais-inscriptions.index')
            ->with('success', 'Frais d\'inscription supprimé avec succès.');
    }
}
