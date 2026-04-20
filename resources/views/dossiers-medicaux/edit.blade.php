<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier le dossier médical') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('dossier-medicals.show', $dossier->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← {{ __('Retour au dossier') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            {{-- Profile Photo Preview --}}
            <div class="mb-6 flex items-center space-x-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                @if($dossier->photo_profil_path)
                    <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                         alt="{{ $dossier->nom_complet }}"
                         class="w-10 h-10 min-w-[40px] max-w-[40px] min-h-[40px] max-h-[40px] rounded-full object-cover shadow-md border-2 border-white dark:border-gray-600 flex-shrink-0">
                @else
                    <div class="w-10 h-10 min-w-[40px] max-w-[40px] rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($dossier->prenom, 0, 1).substr($dossier->nom, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $dossier->prenom }} {{ $dossier->nom }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                        {{ $dossier->numero_unique }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8 lg:p-10">
                    <form method="POST" action="{{ route('dossier-medicals.update', $dossier->id) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Informations personnelles') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="prenom" :value="__('Prénom *')" />
                                        <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full"
                                                      :value="old('prenom', $dossier->prenom)" required />
                                        <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="nom" :value="__('Nom *')" />
                                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
                                                      :value="old('nom', $dossier->nom)" required />
                                        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="date_naissance" :value="__('Date de naissance')" />
                                        <x-text-input id="date_naissance" name="date_naissance" type="date"
                                                      class="mt-1 block w-full"
                                                      :value="old('date_naissance', optional($dossier->date_naissance)->format('Y-m-d'))" />
                                        <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="sexe" :value="__('Sexe')" />
                                        <select id="sexe" name="sexe"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="">{{ __('Sélectionner') }}</option>
                                            <option value="M" @selected(old('sexe', $dossier->sexe) === 'M')>Masculin</option>
                                            <option value="F" @selected(old('sexe', $dossier->sexe) === 'F')>Féminin</option>
                                            <option value="Autre" @selected(old('sexe', $dossier->sexe) === 'Autre')>Autre</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('sexe')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="telephone" :value="__('Téléphone')" />
                                        <x-text-input id="telephone" name="telephone" type="text"
                                                      class="mt-1 block w-full" :value="old('telephone', $dossier->telephone)" />
                                        <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="adresse" :value="__('Adresse')" />
                                    <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full"
                                                  :value="old('adresse', $dossier->adresse)" />
                                    <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Inscription') }}
                                </h3>

                                <div>
                                    <x-input-label for="frais_id" :value="__('Frais d\'inscription (type = inscription)')" />
                                    <select id="frais_id" name="frais_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <option value="">{{ __('Sélectionner') }}</option>
                                        @foreach(($fraisInscriptions ?? []) as $frais)
                                            <option value="{{ $frais->id }}" @selected(old('frais_id', $dossier->frais_id) == $frais->id)>
                                                {{ $frais->libelle }} ({{ number_format($frais->prix, 2) }} XAF)
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('frais_id')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="statut_paiement_inscription" :value="__('Statut paiement')" />
                                        <select id="statut_paiement_inscription" name="statut_paiement_inscription"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="en_attente" @selected(old('statut_paiement_inscription', $dossier->statut_paiement_inscription) === 'en_attente')>En attente</option>
                                            <option value="paye" @selected(old('statut_paiement_inscription', $dossier->statut_paiement_inscription) === 'paye')>Payé</option>
                                            <option value="exonere" @selected(old('statut_paiement_inscription', $dossier->statut_paiement_inscription) === 'exonere')>Exonéré</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('statut_paiement_inscription')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="mode_paiement_inscription" :value="__('Mode paiement')" />
                                        <select id="mode_paiement_inscription" name="mode_paiement_inscription"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="">{{ __('Sélectionner') }}</option>
                                            <option value="cash" @selected(old('mode_paiement_inscription', $dossier->mode_paiement_inscription) === 'cash')>Cash</option>
                                            <option value="en_ligne" @selected(old('mode_paiement_inscription', $dossier->mode_paiement_inscription) === 'en_ligne')>En ligne</option>
                                            <option value="mobile_money" @selected(old('mode_paiement_inscription', $dossier->mode_paiement_inscription) === 'mobile_money')>Mobile money</option>
                                            <option value="carte" @selected(old('mode_paiement_inscription', $dossier->mode_paiement_inscription) === 'carte')>Carte</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('mode_paiement_inscription')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="reference_paiement_inscription" :value="__('Référence paiement')" />
                                    <x-text-input id="reference_paiement_inscription" name="reference_paiement_inscription" type="text"
                                                  class="mt-1 block w-full" :value="old('reference_paiement_inscription', $dossier->reference_paiement_inscription)" />
                                    <x-input-error :messages="$errors->get('reference_paiement_inscription')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center space-x-3">
                                        <input id="actif" name="actif" type="checkbox" value="1"
                                               class="rounded border-gray-300 dark:border-gray-600"
                                               @checked(old('actif', $dossier->actif))>
                                        <label for="actif" class="text-sm text-gray-700 dark:text-gray-200">{{ __('Dossier actif') }}</label>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <input id="partage_actif" name="partage_actif" type="checkbox" value="1"
                                               class="rounded border-gray-300 dark:border-gray-600"
                                               @checked(old('partage_actif', $dossier->partage_actif))>
                                        <label for="partage_actif" class="text-sm text-gray-700 dark:text-gray-200">{{ __('Partage actif') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ route('dossier-medicals.show', $dossier) }}"
                               class="px-8 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200 font-medium border border-gray-300 dark:border-gray-500">
                                {{ __('Annuler') }}
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                {{ __('Mettre à jour') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

