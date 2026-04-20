@extends('layouts.app')

@section('title', 'Modifier le Paiement')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Modifier le Paiement</h2>
                <a href="{{ route('paiements.show', $paiement) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Annuler
                </a>
            </div>

            <form action="{{ route('paiements.update', $paiement) }}" method="POST" x-data="{ type: '{{ $paiement->type_paiement }}' }">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type de Paiement -->
                    <div>
                        <label for="type_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de Paiement</label>
                        <select name="type_paiement" id="type_paiement" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @change="type = $event.target.value">
                            <option value="inscription" {{ old('type_paiement', $paiement->type_paiement) === 'inscription' ? 'selected' : '' }}>Inscription</option>
                            <option value="reabonnement" {{ old('type_paiement', $paiement->type_paiement) === 'reabonnement' ? 'selected' : '' }}>Réabonnement</option>
                        </select>
                        @error('type_paiement')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Frais d'Inscription -->
                    <div id="frais_inscription_field" x-show="type === 'inscription'" x-transition>
                        <label for="frais_inscription_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frais d'Inscription</label>
                        <select name="frais_inscription_id" id="frais_inscription_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner un frais d'inscription</option>
                            @foreach(\App\Models\Frais::where('type', 'inscription')->get() as $frais)
                                <option value="{{ $frais->id }}" {{ old('frais_inscription_id', $paiement->frais_inscription_id) == $frais->id ? 'selected' : '' }}>
                                    {{ $frais->libelle }} - {{ $frais->prix_formatted }}
                                </option>
                            @endforeach
                        </select>
                        @error('frais_inscription_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Montant (€)</label>
                        <input type="number" step="0.01" name="montant" id="montant" value="{{ old('montant', $paiement->montant) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('montant')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre de Mois -->
                    <div id="nombre_mois_field" style="{{ $paiement->type_paiement === 'inscription' ? 'display: none;' : '' }}">
                        <label for="nombre_mois" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de Mois</label>
                        <input type="number" min="1" max="12" name="nombre_mois" id="nombre_mois" value="{{ old('nombre_mois', $paiement->nombre_mois) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nombre_mois')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Période Début -->
                    <div>
                        <label for="periode_debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Période Début</label>
                        <input type="date" name="periode_debut" id="periode_debut" value="{{ old('periode_debut', $paiement->periode_debut ? $paiement->periode_debut->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('periode_debut')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Période Fin -->
                    <div>
                        <label for="periode_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Période Fin</label>
                        <input type="date" name="periode_fin" id="periode_fin" value="{{ old('periode_fin', $paiement->periode_fin ? $paiement->periode_fin->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('periode_fin')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                        <select name="statut" id="statut" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="en_attente" {{ old('statut', $paiement->statut) === 'en_attente' ? 'selected' : '' }}>En Attente</option>
                            <option value="paye" {{ old('statut', $paiement->statut) === 'paye' ? 'selected' : '' }}>Payé</option>
                            <option value="annule" {{ old('statut', $paiement->statut) === 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mode de Paiement -->
                    <div>
                        <label for="mode_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mode de Paiement</label>
                        <select name="mode_paiement" id="mode_paiement" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="especes" {{ old('mode_paiement', $paiement->mode_paiement) === 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="cheque" {{ old('mode_paiement', $paiement->mode_paiement) === 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="virement" {{ old('mode_paiement', $paiement->mode_paiement) === 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="carte_bancaire" {{ old('mode_paiement', $paiement->mode_paiement) === 'carte_bancaire' ? 'selected' : '' }}>Carte Bancaire</option>
                        </select>
                        @error('mode_paiement')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Référence de Paiement -->
                    <div>
                        <label for="reference_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Référence de Paiement</label>
                        <input type="text" name="reference_paiement" id="reference_paiement" value="{{ old('reference_paiement', $paiement->reference_paiement) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('reference_paiement')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'Encaissement -->
                    <div>
                        <label for="date_encaissement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date d'Encaissement</label>
                        <input type="datetime-local" name="date_encaissement" id="date_encaissement" value="{{ old('date_encaissement', $paiement->date_encaissement ? $paiement->date_encaissement->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('date_encaissement')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $paiement->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Mettre à Jour le Paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('type_paiement').addEventListener('change', function() {
    const nombreMoisField = document.getElementById('nombre_mois_field');
    const fraisInscriptionField = document.getElementById('frais_inscription_field');

    if (this.value === 'reabonnement') {
        nombreMoisField.style.display = 'block';
        fraisInscriptionField.style.display = 'none';
    } else {
        nombreMoisField.style.display = 'none';
        fraisInscriptionField.style.display = 'block';
    }
});

// Trigger change event on page load to set initial state
document.getElementById('type_paiement').dispatchEvent(new Event('change'));
</script>
@endsection
