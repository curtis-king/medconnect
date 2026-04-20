<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier le Dossier Médical') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('dossier-medicals.update', $dossier->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="numero_unique" :value="__('N° Dossier')" />
                            <x-text-input id="numero_unique" name="numero_unique" type="text"
                                          class="mt-1 block w-full" :value="old('numero_unique', $dossier->numero_unique)"
                                          readonly />
                            <x-input-error :messages="$errors->get('numero_unique')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="source_creation" :value="__('Source de Création')" />
                            <select name="source_creation" id="source_creation" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="guichet" {{ old('source_creation', $dossier->source_creation) == 'guichet' ? 'selected' : '' }}>Guichet</option>
                                <option value="en_ligne" {{ old('source_creation', $dossier->source_creation) == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                            </select>
                            <x-input-error :messages="$errors->get('source_creation')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="nom" :value="__('Nom')" />
                                <x-text-input id="nom" name="nom" type="text"
                                              class="mt-1 block w-full" :value="old('nom', $dossier->nom)" required />
                                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="prenom" :value="__('Prénom')" />
                                <x-text-input id="prenom" name="prenom" type="text"
                                              class="mt-1 block w-full" :value="old('prenom', $dossier->prenom)" required />
                                <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="date_naissance" :value="__('Date de Naissance')" />
                                <x-text-input id="date_naissance" name="date_naissance" type="date"
                                              class="mt-1 block w-full" :value="old('date_naissance', $dossier->date_naissance ? $dossier->date_naissance->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sexe" :value="__('Sexe')" />
                                <select name="sexe" id="sexe" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Sélectionner</option>
                                    <option value="M" {{ old('sexe', $dossier->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe', $dossier->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                <x-input-error :messages="$errors->get('sexe')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="telephone" :value="__('Téléphone')" />
                                <x-text-input id="telephone" name="telephone" type="tel"
                                              class="mt-1 block w-full" :value="old('telephone', $dossier->telephone)" />
                                <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="groupe_sanguin" :value="__('Groupe Sanguin')" />
                                <x-text-input id="groupe_sanguin" name="groupe_sanguin" type="text"
                                              class="mt-1 block w-full" :value="old('groupe_sanguin', $dossier->groupe_sanguin)" />
                                <x-input-error :messages="$errors->get('groupe_sanguin')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="adresse" :value="__('Adresse')" />
                            <textarea id="adresse" name="adresse"
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                      rows="2">{{ old('adresse', $dossier->adresse) }}</textarea>
                            <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="frais_inscription_id" :value="__('Type de Frais d\'Inscription')" />
                            <select name="frais_inscription_id" id="frais_inscription_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Sélectionner un type de frais</option>
                                @foreach($fraisInscriptions as $frais)
                                    <option value="{{ $frais->id }}" {{ old('frais_inscription_id', $dossier->frais_inscription_id) == $frais->id ? 'selected' : '' }}>
                                        {{ $frais->libelle }} ({{ $frais->prix }}€)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('frais_inscription_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="statut_paiement_inscription" :value="__('Statut du Paiement')" />
                            <select name="statut_paiement_inscription" id="statut_paiement_inscription" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="en_attente" {{ old('statut_paiement_inscription', $dossier->statut_paiement_inscription) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="paye" {{ old('statut_paiement_inscription', $dossier->statut_paiement_inscription) == 'paye' ? 'selected' : '' }}>Payé</option>
                                <option value="exonere" {{ old('statut_paiement_inscription', $dossier->statut_paiement_inscription) == 'exonere' ? 'selected' : '' }}>Exonéré</option>
                            </select>
                            <x-input-error :messages="$errors->get('statut_paiement_inscription')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="mode_paiement_inscription" :value="__('Mode de Paiement')" />
                                <select name="mode_paiement_inscription" id="mode_paiement_inscription" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Sélectionner</option>
                                    <option value="cash" {{ old('mode_paiement_inscription', $dossier->mode_paiement_inscription) == 'cash' ? 'selected' : '' }}>Espèces</option>
                                    <option value="en_ligne" {{ old('mode_paiement_inscription', $dossier->mode_paiement_inscription) == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                                    <option value="mobile_money" {{ old('mode_paiement_inscription', $dossier->mode_paiement_inscription) == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="carte" {{ old('mode_paiement_inscription', $dossier->mode_paiement_inscription) == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                </select>
                                <x-input-error :messages="$errors->get('mode_paiement_inscription')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="reference_paiement_inscription" :value="__('Référence de Paiement')" />
                                <x-text-input id="reference_paiement_inscription" name="reference_paiement_inscription" type="text"
                                              class="mt-1 block w-full" :value="old('reference_paiement_inscription', $dossier->reference_paiement_inscription)" />
                                <x-input-error :messages="$errors->get('reference_paiement_inscription')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dossier-medicals.show', $dossier->id) }}"
                               class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                Annuler
                            </a>
                            <x-primary-button>
                                {{ __('Mettre à Jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
