<?php

namespace App\Http\Controllers;

use App\Models\ServiceMedical;
use Illuminate\Http\Request;

class ServiceMedicalController extends Controller
{
    public function index()
    {
        $services = ServiceMedical::orderBy('type')->orderBy('nom')->get();

        return view('services-medicaux.index', compact('services'));
    }

    public function create()
    {
        return view('services-medicaux.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'type' => 'required|string',
            'actif' => 'nullable|boolean',
        ]);

        ServiceMedical::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'type' => $request->type,
            'actif' => $request->actif ?? true,
        ]);

        return redirect()->route('services-medicaux.index')->with('success', 'Service créé avec succès');
    }

    public function edit(ServiceMedical $service)
    {
        return view('services-medicaux.edit', compact('service'));
    }

    public function update(Request $request, ServiceMedical $service)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'type' => 'required|string',
            'actif' => 'nullable|boolean',
        ]);

        $service->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'type' => $request->type,
            'actif' => $request->actif ?? true,
        ]);

        return redirect()->route('services-medicaux.index')->with('success', 'Service mis à jour');
    }

    public function destroy(ServiceMedical $service)
    {
        $service->delete();

        return redirect()->route('services-medicaux.index')->with('success', 'Service supprimé');
    }
}
