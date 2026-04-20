<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Enregistrer un Paiement') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('dossier-medicals.show', $dossier->id) }}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Retour au dossier médical
                        </a>
                    </div>

                    <form method="POST" action="{{ route('paiements.store') }}">
                        @csrf

                        <input type="hidden" name="dossier_medical_id" value="{{ $dossier->id }}">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dossier Médical</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                {{ $dossier->numero_unique }} - {{ $dossier->nom_complet }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="type_paiement" :value="__('Type de Paiement')" />
                            <select name="type_paiement" id="type_paiement" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="reabonnement" {{ old('type_paiement', 'reabonnement') == 'reabonnement' ? 'selected' : '' }}>Réabonnement</option>
                                <option value="inscription" {{ old('type_paiement') == 'inscription' ? 'selected' : '' }}>Inscription</option>
                            </select>
                            <x-input-error :messages="$errors->get('type_paiement')" class="mt-2" />
                        </div>

                        <div class="mb-4" id="frais_inscription_field" style="display: none;">
                            <x-input-label for="frais_inscription_id" :value="__('Frais d\'Inscription')" />
                            <select name="frais_inscription_id" id="frais_inscription_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Sélectionner un frais d'inscription</option>
                                @foreach(\App\Models\Frais::where('type', 'inscription')->get() as $frais)
                                    <option value="{{ $frais->id }}" {{ old('frais_inscription_id') == $frais->id ? 'selected' : '' }}>
                                        {{ $frais->libelle }} - {{ $frais->prix_formatted }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('frais_inscription_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="periode_debut" :value="__('Date de Début')" />
                                <x-text-input id="periode_debut" name="periode_debut" type="date"
                                              class="mt-1 block w-full"
                                              :value="old('periode_debut', $prochainePeriodeDebut ? $prochainePeriodeDebut->format('Y-m-d') : '')" required />
                                <x-input-error :messages="$errors->get('periode_debut')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nombre_mois" :value="__('Nombre de Mois')" />
                                <select name="nombre_mois" id="nombre_mois" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('nombre_mois', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} mois{{ $i > 1 ? '' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('nombre_mois')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="montant" :value="__('Montant (€)')" />
                            <x-text-input id="montant" name="montant" type="number" step="0.01"
                                          class="mt-1 block w-full" :value="old('montant')" required />
                            <x-input-error :messages="$errors->get('montant')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="mode_paiement" :value="__('Mode de Paiement')" />
                            <select name="mode_paiement" id="mode_paiement" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="cash" {{ old('mode_paiement', 'cash') == 'cash' ? 'selected' : '' }}>Espèces</option>
                                <option value="carte" {{ old('mode_paiement') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="en_ligne" {{ old('mode_paiement') == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                                <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                            </select>
                            <x-input-error :messages="$errors->get('mode_paiement')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reference_paiement" :value="__('Référence de Paiement')" />
                            <x-text-input id="reference_paiement" name="reference_paiement" type="text"
                                          class="mt-1 block w-full" :value="old('reference_paiement')" />
                            <x-input-error :messages="$errors->get('reference_paiement')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes"
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                      rows="3">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dossier-medicals.show', $dossier->id) }}"
                               class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                Annuler
                            </a>
                            <x-primary-button>
                                {{ __('Enregistrer le Paiement') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('type_paiement').addEventListener('change', function() {
            const fraisInscriptionField = document.getElementById('frais_inscription_field');
            if (this.value === 'inscription') {
                fraisInscriptionField.style.display = 'block';
            } else {
                fraisInscriptionField.style.display = 'none';
            }
        });

        // Trigger change event on page load to set initial state
        document.getElementById('type_paiement').dispatchEvent(new Event('change'));
    </script>
</x-app-layout>
