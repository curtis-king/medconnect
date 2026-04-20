<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nouveau dossier médical') }}
            </h2>
            <div class="mt-2">
                <a href="{{ ($selfService ?? false) ? route('dashboard') : route('dossier-medicals.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8 lg:p-10">
                    <form method="POST" action="{{ ($selfService ?? false) ? route('user.adherer.store') : route('dossier-medicals.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <input type="hidden" name="source_creation" value="{{ ($selfService ?? false) ? 'en_ligne' : 'guichet' }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Informations personnelles') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="prenom" :value="__('Prénom *')" />
                                        <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full"
                                                      :value="old('prenom')" required autofocus />
                                        <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="nom" :value="__('Nom *')" />
                                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
                                                      :value="old('nom')" required />
                                        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="date_naissance" :value="__('Date de naissance')" />
                                        <x-text-input id="date_naissance" name="date_naissance" type="date"
                                                      class="mt-1 block w-full" :value="old('date_naissance')" />
                                        <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="sexe" :value="__('Sexe')" />
                                        <select id="sexe" name="sexe"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="">{{ __('Sélectionner') }}</option>
                                            <option value="M" @selected(old('sexe') === 'M')>Masculin</option>
                                            <option value="F" @selected(old('sexe') === 'F')>Féminin</option>
                                            <option value="Autre" @selected(old('sexe') === 'Autre')>Autre</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('sexe')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="telephone" :value="__('Téléphone')" />
                                        <x-text-input id="telephone" name="telephone" type="text"
                                                      class="mt-1 block w-full" :value="old('telephone')" />
                                        <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="adresse" :value="__('Adresse')" />
                                    <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full"
                                                  :value="old('adresse')" />
                                    <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Photo de profil & pièce') }}
                                </h3>

                                <div class="flex items-center space-x-6">
                                    <div
                                        class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-semibold text-xl">
                                        {{ strtoupper(substr(old('prenom', 'P')[0], 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                             <x-input-label for="photo_profil" :value="__('Photo de profil *')" />
                                        <input id="photo_profil" name="photo_profil" type="file"
                                                 required
                                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-full file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-blue-50 file:text-blue-700
                                                      hover:file:bg-blue-100" />
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ __('Image max 2 Mo (JPG, PNG, etc.)') }}
                                        </p>
                                        <x-input-error :messages="$errors->get('photo_profil')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="type_piece_identite" :value="__('Type de pièce d\'identité')" />
                                        <select id="type_piece_identite" name="type_piece_identite"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                            <option value="">{{ __('Sélectionner') }}</option>
                                            <option value="cni" @selected(old('type_piece_identite') === 'cni')>CNI</option>
                                            <option value="passeport" @selected(old('type_piece_identite') === 'passeport')>Passeport</option>
                                            <option value="permis" @selected(old('type_piece_identite') === 'permis')>Permis</option>
                                            <option value="autre" @selected(old('type_piece_identite') === 'autre')>Autre</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('type_piece_identite')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="numero_piece_identite" :value="__('Numéro de pièce')" />
                                        <x-text-input id="numero_piece_identite" name="numero_piece_identite" type="text"
                                                      class="mt-1 block w-full" :value="old('numero_piece_identite')" />
                                        <x-input-error :messages="$errors->get('numero_piece_identite')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="date_expiration_piece_identite" :value="__('Date d\'expiration')" />
                                        <x-text-input id="date_expiration_piece_identite" name="date_expiration_piece_identite" type="date"
                                                      class="mt-1 block w-full" :value="old('date_expiration_piece_identite')" />
                                        <x-input-error :messages="$errors->get('date_expiration_piece_identite')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="piece_identite_recto" :value="__('Pièce recto (image)')" />
                                        <input id="piece_identite_recto" name="piece_identite_recto" type="file"
                                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300" />
                                        <x-input-error :messages="$errors->get('piece_identite_recto')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="piece_identite_verso" :value="__('Pièce verso (image)')" />
                                        <input id="piece_identite_verso" name="piece_identite_verso" type="file"
                                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300" />
                                        <x-input-error :messages="$errors->get('piece_identite_verso')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Informations médicales') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="groupe_sanguin" :value="__('Groupe sanguin')" />
                                        <x-text-input id="groupe_sanguin" name="groupe_sanguin" type="text"
                                                      class="mt-1 block w-full" :value="old('groupe_sanguin')" placeholder="Ex: A+, O-" />
                                        <x-input-error :messages="$errors->get('groupe_sanguin')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="allergies" :value="__('Allergies')" />
                                    <textarea id="allergies" name="allergies" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('allergies') }}</textarea>
                                    <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="maladies_chroniques" :value="__('Maladies chroniques')" />
                                    <textarea id="maladies_chroniques" name="maladies_chroniques" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('maladies_chroniques') }}</textarea>
                                    <x-input-error :messages="$errors->get('maladies_chroniques')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="traitements_en_cours" :value="__('Traitements en cours')" />
                                    <textarea id="traitements_en_cours" name="traitements_en_cours" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('traitements_en_cours') }}</textarea>
                                    <x-input-error :messages="$errors->get('traitements_en_cours')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="antecedents_familiaux" :value="__('Antécédents familiaux')" />
                                    <textarea id="antecedents_familiaux" name="antecedents_familiaux" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('antecedents_familiaux') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedents_familiaux')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="antecedents_personnels" :value="__('Antécédents personnels')" />
                                    <textarea id="antecedents_personnels" name="antecedents_personnels" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('antecedents_personnels') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedents_personnels')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('Contact d\'urgence') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="contact_urgence_nom" :value="__('Nom complet')" />
                                        <x-text-input id="contact_urgence_nom" name="contact_urgence_nom" type="text"
                                                      class="mt-1 block w-full" :value="old('contact_urgence_nom')" />
                                        <x-input-error :messages="$errors->get('contact_urgence_nom')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="contact_urgence_telephone" :value="__('Téléphone')" />
                                        <x-text-input id="contact_urgence_telephone" name="contact_urgence_telephone" type="text"
                                                      class="mt-1 block w-full" :value="old('contact_urgence_telephone')" />
                                        <x-input-error :messages="$errors->get('contact_urgence_telephone')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="contact_urgence_relation" :value="__('Relation avec le patient')" />
                                    <x-text-input id="contact_urgence_relation" name="contact_urgence_relation" type="text"
                                                  class="mt-1 block w-full" :value="old('contact_urgence_relation')" />
                                    <x-input-error :messages="$errors->get('contact_urgence_relation')" class="mt-2" />
                                </div>

                                <div class="mt-8 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-green-50 dark:from-gray-700 dark:to-gray-600 border border-blue-100 dark:border-gray-600">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2">
                                        {{ __('Inscription & paiement') }}
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                        {{ __('Ce dossier servira d\'inscription. Le paiement pourra être validé par la caisse ou réalisé en ligne selon le canal de création.') }}
                                    </p>
                                    @isset($fraisInscriptions)
                                        <div class="mt-4">
                                            <x-input-label for="frais_id" :value="__('Frais d\'inscription (type = inscription)')" />
                                            <select id="frais_id" name="frais_id"
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                <option value="">{{ __('Sélectionner') }}</option>
                                                @foreach($fraisInscriptions as $frais)
                                                    <option value="{{ $frais->id }}" @selected(old('frais_id') == $frais->id)>
                                                        {{ $frais->libelle }} ({{ number_format($frais->prix, 2) }} XAF)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('frais_id')" class="mt-2" />
                                        </div>
                                    @endisset
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ ($selfService ?? false) ? route('dashboard') : route('dossier-medicals.index') }}"
                               class="px-8 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200 font-medium border border-gray-300 dark:border-gray-500">
                                {{ __('Annuler') }}
                            </a>
                            <button type="submit"
                                    class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                {{ __('Enregistrer le dossier') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

