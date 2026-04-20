<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ ($modeDeclaration ?? 'personnel') === 'dependant' ? 'Declarer un enfant / personne a charge - Etape 1/2' : 'Adherer - Etape 1/2' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ ($modeDeclaration ?? 'personnel') === 'dependant' ? 'Remplissez le dossier medical de la personne a charge depuis votre compte.' : 'Remplissez votre dossier personnel.' }}
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">
                        {{ ($modeDeclaration ?? 'personnel') === 'dependant' ? 'Creation de dossier medical a charge' : 'Creation de dossier medical utilisateur' }}
                    </h3>
                    <p class="text-blue-100 text-sm mt-1">
                        {{ ($modeDeclaration ?? 'personnel') === 'dependant' ? 'Ce dossier sera rattache a votre compte pour les rendez-vous et abonnements.' : 'Ce dossier sera automatiquement lie a votre compte.' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('user.adherer.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="source_creation" value="en_ligne">
                    <input type="hidden" name="declaration_mode" value="{{ ($modeDeclaration ?? 'personnel') === 'dependant' ? 'dependant' : 'personnel' }}">

                    @if(($modeDeclaration ?? 'personnel') === 'dependant')
                        <div class="rounded-xl border border-teal-200 dark:border-teal-700 bg-teal-50/70 dark:bg-teal-900/20 p-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Lien avec le titulaire du compte *</label>
                            <select name="lien_avec_responsable" class="w-full px-3 py-2 rounded-lg border border-teal-300 dark:border-teal-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required>
                                <option value="">Selectionner la relation</option>
                                <option value="enfant" @selected(old('lien_avec_responsable') === 'enfant')>Enfant</option>
                                <option value="conjoint" @selected(old('lien_avec_responsable') === 'conjoint')>Conjoint(e)</option>
                                <option value="parent" @selected(old('lien_avec_responsable') === 'parent')>Parent</option>
                                <option value="frere_soeur" @selected(old('lien_avec_responsable') === 'frere_soeur')>Frere / Soeur</option>
                                <option value="autre" @selected(old('lien_avec_responsable') === 'autre')>Autre personne a charge</option>
                            </select>
                            @error('lien_avec_responsable')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prenom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('prenom')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('nom')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date naissance</label>
                            <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexe</label>
                            <select name="sexe" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Selectionner</option>
                                <option value="M" @selected(old('sexe') === 'M')>Masculin</option>
                                <option value="F" @selected(old('sexe') === 'F')>Feminin</option>
                                <option value="Autre" @selected(old('sexe') === 'Autre')>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telephone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de profil *</label>
                            <div class="rounded-xl border border-dashed border-blue-300 dark:border-blue-700 p-3 bg-blue-50/60 dark:bg-blue-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="photo_profil_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir une photo
                                    </label>
                                    <span id="photo_profil_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="photo_profil_input" type="file" name="photo_profil" accept="image/*" required class="hidden" onchange="updateImagePreview(this, 'photo_profil_preview', 'photo_profil_name')">
                                <img id="photo_profil_preview" src="" alt="Prévisualisation photo" class="hidden mt-3 w-20 h-20 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                            </div>
                            @error('photo_profil')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type piece identite</label>
                            <select name="type_piece_identite" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Selectionner</option>
                                <option value="cni" @selected(old('type_piece_identite') === 'cni')>CNI</option>
                                <option value="passeport" @selected(old('type_piece_identite') === 'passeport')>Passeport</option>
                                <option value="permis" @selected(old('type_piece_identite') === 'permis')>Permis</option>
                                <option value="autre" @selected(old('type_piece_identite') === 'autre')>Autre</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Obligatoire pour le titulaire du compte et toute personne a charge de 18 ans ou plus. Pour un enfant mineur, facultatif.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero piece</label>
                            <input type="text" name="numero_piece_identite" value="{{ old('numero_piece_identite') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date expiration piece</label>
                            <input type="date" name="date_expiration_piece_identite" value="{{ old('date_expiration_piece_identite') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce recto</label>
                            <div class="rounded-xl border border-dashed border-blue-300 dark:border-blue-700 p-3 bg-blue-50/60 dark:bg-blue-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="recto_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir le recto
                                    </label>
                                    <span id="recto_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="recto_input" type="file" name="piece_identite_recto" accept="image/*" class="hidden" onchange="updateImagePreview(this, 'recto_preview', 'recto_name')">
                                <img id="recto_preview" src="" alt="Prévisualisation recto" class="hidden mt-3 w-40 h-24 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                            </div>
                            @error('piece_identite_recto')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce verso</label>
                            <div class="rounded-xl border border-dashed border-blue-300 dark:border-blue-700 p-3 bg-blue-50/60 dark:bg-blue-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="verso_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir le verso
                                    </label>
                                    <span id="verso_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="verso_input" type="file" name="piece_identite_verso" accept="image/*" class="hidden" onchange="updateImagePreview(this, 'verso_preview', 'verso_name')">
                                <img id="verso_preview" src="" alt="Prévisualisation verso" class="hidden mt-3 w-40 h-24 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                            </div>
                            @error('piece_identite_verso')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-900">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frais d'inscription *</label>
                        <select name="frais_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Selectionner</option>
                            @foreach($fraisInscriptions as $frais)
                                <option value="{{ $frais->id }}" @selected(old('frais_id') == $frais->id)>
                                    {{ $frais->libelle }} - {{ number_format((float) $frais->prix, 0, ',', ' ') }} XAF
                                </option>
                            @endforeach
                        </select>
                        @error('frais_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Annuler</a>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                            Continuer vers paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateImagePreview(input, previewId, nameId) {
            const preview = document.getElementById(previewId);
            const name = document.getElementById(nameId);
            const file = input.files?.[0];

            if (!file) {
                if (name) name.textContent = 'Aucun fichier sélectionné';
                if (preview) {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
                return;
            }

            if (name) {
                name.textContent = file.name;
            }

            if (preview && file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>
