<?php

namespace App\Http\Controllers;

use App\Models\Frais;
use Illuminate\Http\Request;

class FraisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $frais = Frais::all();

        return view('frais.index', compact('frais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Frais::TYPES;

        return view('frais.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'type' => 'required|in:inscription,reabonnement,contribution',
            'detail' => 'nullable|string',
        ]);

        Frais::create($request->all());

        return redirect()->route('frais.index')
            ->with('success', 'Frais créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Frais $frai)
    {
        return view('frais.show', compact('frai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Frais $frai)
    {
        $types = Frais::TYPES;

        return view('frais.edit', compact('frai', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frais $frai)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'type' => 'required|in:inscription,reabonnement,contribution',
            'detail' => 'nullable|string',
        ]);

        $frai->update($request->all());

        return redirect()->route('frais.index')
            ->with('success', 'Frais mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frais $frai)
    {
        $frai->delete();

        return redirect()->route('frais.index')
            ->with('success', 'Frais supprimé avec succès.');
    }
}
