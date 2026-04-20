<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Créer un Dossier Médical') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <form method="POST" action="{{ route('dossier-medicals.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Source de Création -->
                        <div class="mb-6">
                            <x-input-label for="source_creation" :value="__('Source de Création')" />
                            <select name="source_creation" id="source_creation" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="guichet" {{ old('source_creation', 'guichet') == 'guichet' ? 'selected' : '' }}>Guichet</option>
                                <option value="en_ligne" {{ old('source_creation') == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                            </select>
                            <x-input-error :messages="$errors->get('source_creation')" class="mt-2" />
                        </div>

                        <!-- Informations Personnelles -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Informations Personnelles
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="nom" :value="__('Nom')" />
                                    <x-text-input id="nom" name="nom" type="text"
                                                  class="mt-1 block w-full" :value="old('nom')" required />
                                    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="prenom" :value="__('Prénom')" />
                                    <x-text-input id="prenom" name="prenom" type="text"
                                                  class="mt-1 block w-full" :value="old('prenom')" required />
                                    <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <x-input-label for="date_naissance" :value="__('Date de Naissance')" />
                                    <x-text-input id="date_naissance" name="date_naissance" type="date"
                                                  class="mt-1 block w-full" :value="old('date_naissance')" />
                                    <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="sexe" :value="__('Sexe')" />
                                    <select name="sexe" id="sexe" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionner</option>
                                        <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('sexe')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="groupe_sanguin" :value="__('Groupe Sanguin')" />
                                    <select name="groupe_sanguin" id="groupe_sanguin" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionner</option>
                                        <option value="A+" {{ old('groupe_sanguin') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('groupe_sanguin') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('groupe_sanguin') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('groupe_sanguin') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('groupe_sanguin') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('groupe_sanguin') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('groupe_sanguin') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('groupe_sanguin') == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('groupe_sanguin')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="telephone" :value="__('Téléphone')" />
                                    <x-text-input id="telephone" name="telephone" type="tel"
                                                  class="mt-1 block w-full" :value="old('telephone')" />
                                    <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email"
                                                  class="mt-1 block w-full" :value="old('email')" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="adresse" :value="__('Adresse Complète')" />
                                <textarea id="adresse" name="adresse"
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          rows="3" placeholder="Adresse complète avec ville, quartier, etc.">{{ old('adresse') }}</textarea>
                                <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Informations Médicales -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                🏥 Informations Médicales
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="allergies" :value="__('Allergies')" />
                                    <textarea id="allergies" name="allergies"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              rows="2" placeholder="Liste des allergies (médicaments, aliments, etc.)">{{ old('allergies') }}</textarea>
                                    <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="maladies_chroniques" :value="__('Maladies Chroniques')" />
                                    <textarea id="maladies_chroniques" name="maladies_chroniques"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              rows="2" placeholder="Diabète, hypertension, etc.">{{ old('maladies_chroniques') }}</textarea>
                                    <x-input-error :messages="$errors->get('maladies_chroniques')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="traitements_en_cours" :value="__('Traitements en Cours')" />
                                    <textarea id="traitements_en_cours" name="traitements_en_cours"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              rows="2" placeholder="Médicaments actuels et posologies">{{ old('traitements_en_cours') }}</textarea>
                                    <x-input-error :messages="$errors->get('traitements_en_cours')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="antecedents_personnels" :value="__('Antécédents Personnels')" />
                                    <textarea id="antecedents_personnels" name="antecedents_personnels"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              rows="2" placeholder="Chirurgies, hospitalisations, etc.">{{ old('antecedents_personnels') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedents_personnels')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="antecedents_familiaux" :value="__('Antécédents Familiaux')" />
                                <textarea id="antecedents_familiaux" name="antecedents_familiaux"
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          rows="2" placeholder="Maladies dans la famille (cancers, diabète, etc.)">{{ old('antecedents_familiaux') }}</textarea>
                                <x-input-error :messages="$errors->get('antecedents_familiaux')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Contact d'Urgence -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                 Contact d'Urgence
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <x-input-label for="contact_urgence_nom" :value="__('Nom du Contact')" />
                                    <x-text-input id="contact_urgence_nom" name="contact_urgence_nom" type="text"
                                                  class="mt-1 block w-full" :value="old('contact_urgence_nom')" />
                                    <x-input-error :messages="$errors->get('contact_urgence_nom')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="contact_urgence_telephone" :value="__('Téléphone')" />
                                    <x-text-input id="contact_urgence_telephone" name="contact_urgence_telephone" type="tel"
                                                  class="mt-1 block w-full" :value="old('contact_urgence_telephone')" />
                                    <x-input-error :messages="$errors->get('contact_urgence_telephone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="contact_urgence_relation" :value="__('Relation')" />
                                    <select name="contact_urgence_relation" id="contact_urgence_relation" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionner</option>
                                        <option value="parent" {{ old('contact_urgence_relation') == 'parent' ? 'selected' : '' }}>Parent</option>
                                        <option value="conjoint" {{ old('contact_urgence_relation') == 'conjoint' ? 'selected' : '' }}>Conjoint</option>
                                        <option value="enfant" {{ old('contact_urgence_relation') == 'enfant' ? 'selected' : '' }}>Enfant</option>
                                        <option value="frere_soeur" {{ old('contact_urgence_relation') == 'frere_soeur' ? 'selected' : '' }}>Frère/Sœur</option>
                                        <option value="ami" {{ old('contact_urgence_relation') == 'ami' ? 'selected' : '' }}>Ami</option>
                                        <option value="autre" {{ old('contact_urgence_relation') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('contact_urgence_relation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Pièce d'Identité -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                 Pièce d'Identité
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <x-input-label for="type_piece_identite" :value="__('Type de Pièce')" />
                                    <select name="type_piece_identite" id="type_piece_identite" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionner</option>
                                        <option value="cni" {{ old('type_piece_identite') == 'cni' ? 'selected' : '' }}>Carte Nationale d'Identité</option>
                                        <option value="passeport" {{ old('type_piece_identite') == 'passeport' ? 'selected' : '' }}>Passeport</option>
                                        <option value="permis" {{ old('type_piece_identite') == 'permis' ? 'selected' : '' }}>Permis de Conduire</option>
                                        <option value="autre" {{ old('type_piece_identite') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type_piece_identite')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="numero_piece_identite" :value="__('Numéro de Pièce')" />
                                    <x-text-input id="numero_piece_identite" name="numero_piece_identite" type="text"
                                                  class="mt-1 block w-full" :value="old('numero_piece_identite')" />
                                    <x-input-error :messages="$errors->get('numero_piece_identite')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date_expiration_piece_identite" :value="__('Date d\'Expiration')" />
                                    <x-text-input id="date_expiration_piece_identite" name="date_expiration_piece_identite" type="date"
                                                  class="mt-1 block w-full" :value="old('date_expiration_piece_identite')" />
                                    <x-input-error :messages="$errors->get('date_expiration_piece_identite')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="piece_identite_recto" :value="__('Photo Recto')" />
                                    <input type="file" id="piece_identite_recto" name="piece_identite_recto"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           accept="image/*">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, PDF. Taille max: 4MB</p>
                                    <x-input-error :messages="$errors->get('piece_identite_recto')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="piece_identite_verso" :value="__('Photo Verso')" />
                                    <input type="file" id="piece_identite_verso" name="piece_identite_verso"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           accept="image/*">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, PDF. Taille max: 4MB</p>
                                    <x-input-error :messages="$errors->get('piece_identite_verso')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="photo_profil" :value="__('Photo de Profil')" />
                                <input type="file" id="photo_profil" name="photo_profil"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       accept="image/*">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG. Taille max: 2MB</p>
                                <x-input-error :messages="$errors->get('photo_profil')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Informations de Paiement -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Informations de Paiement
                            </h3>

                            <div class="mb-4">
                                <x-input-label for="frais_inscription_id" :value="__('Type de Frais d\'Inscription')" />
                                <select name="frais_inscription_id" id="frais_inscription_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Sélectionner un type de frais</option>
                                    @foreach($fraisInscriptions as $frais)
                                        <option value="{{ $frais->id }}" {{ old('frais_inscription_id') == $frais->id ? 'selected' : '' }}>
                                            {{ $frais->libelle }} ({{ $frais->prix }}€)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('frais_inscription_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="statut_paiement_inscription" :value="__('Statut du Paiement')" />
                                    <select name="statut_paiement_inscription" id="statut_paiement_inscription" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="en_attente" {{ old('statut_paiement_inscription', 'en_attente') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="paye" {{ old('statut_paiement_inscription') == 'paye' ? 'selected' : '' }}>Payé</option>
                                        <option value="exonere" {{ old('statut_paiement_inscription') == 'exonere' ? 'selected' : '' }}>Exonéré</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('statut_paiement_inscription')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="mode_paiement_inscription" :value="__('Mode de Paiement')" />
                                    <select name="mode_paiement_inscription" id="mode_paiement_inscription" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionner</option>
                                        <option value="cash" {{ old('mode_paiement_inscription') == 'cash' ? 'selected' : '' }}>Espèces</option>
                                        <option value="en_ligne" {{ old('mode_paiement_inscription') == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                                        <option value="mobile_money" {{ old('mode_paiement_inscription') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                        <option value="carte" {{ old('mode_paiement_inscription') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                        <option value="virement" {{ old('mode_paiement_inscription') == 'virement' ? 'selected' : '' }}>Virement</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('mode_paiement_inscription')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="reference_paiement_inscription" :value="__('Référence de Paiement')" />
                                <x-text-input id="reference_paiement_inscription" name="reference_paiement_inscription" type="text"
                                              class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" :value="old('reference_paiement_inscription')" readonly />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">La référence sera générée automatiquement</p>
                                <x-input-error :messages="$errors->get('reference_paiement_inscription')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('dossier-medicals.index') }}"
                               class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200">
                                Annuler
                            </a>
                            <x-primary-button class="px-6 py-2">
                                {{ __('Créer le Dossier') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Générer automatiquement la référence de paiement
        document.addEventListener('DOMContentLoaded', function() {
            const referenceField = document.getElementById('reference_paiement_inscription');
            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            referenceField.value = 'PAY-' + timestamp + '-' + random;
        });
    </script>
</x-app-layout>
