<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mon profil médical</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mettez à jour les informations de votre dossier médical</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(($dossier->documents_validation_statut ?? 'en_attente') === 'en_attente')
                <div class="p-4 rounded-xl bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700 text-sm">
                    Votre profil patient est en attente de validation documentaire. La reponse d activation vous sera envoyee par email.
                </div>
            @endif

            <form method="POST" action="{{ route('user.medical-profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- ===== HEADER CARD WITH PHOTO ===== --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-5">
                        <div class="flex items-center gap-5">
                            {{-- Photo avatar --}}
                            <div class="relative group shrink-0">
                                <div id="photo-preview-wrapper" class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden border-4 border-white/60 shadow-lg bg-white/20 flex items-center justify-center">
                                    @if($dossier->photo_profil_path)
                                        <img id="photo-preview" src="{{ Storage::url($dossier->photo_profil_path) }}" alt="Photo profil" class="block w-full h-full object-cover">
                                    @else
                                        <img id="photo-preview" src="" alt="" class="hidden w-full h-full object-cover">
                                        <div id="photo-placeholder" class="flex flex-col items-center justify-center text-white/70">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <label for="photo_profil_input" class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full flex items-center justify-center cursor-pointer shadow-md hover:bg-gray-100 transition" title="Changer la photo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </label>
                                <input id="photo_profil_input" type="file" name="photo_profil" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                            </div>
                            {{-- Identity info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-xl truncate">{{ $dossier->prenom }} {{ $dossier->nom }}</p>
                                <p class="text-blue-100 text-sm mt-0.5">N° {{ $dossier->numero_unique }}</p>
                                <div class="mt-2 flex items-center gap-2 flex-wrap">
                                    @if($dossier->actif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-400/30 text-white border border-green-300/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-300 inline-block"></span> Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-400/30 text-white border border-yellow-300/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-300 inline-block"></span> En attente
                                        </span>
                                    @endif
                                    @if($dossier->groupe_sanguin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500/40 text-white border border-red-300/40">
                                            🩸 {{ $dossier->groupe_sanguin }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== INFORMATIONS PERSONNELLES ===== --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Informations personnelles</h4>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom *</label>
                                <input type="text" name="prenom" value="{{ old('prenom', $dossier->prenom) }}" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom *</label>
                                <input type="text" name="nom" value="{{ old('nom', $dossier->nom) }}" required
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de naissance</label>
                                <input type="date" name="date_naissance" value="{{ old('date_naissance', optional($dossier->date_naissance)->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexe</label>
                                <select name="sexe" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="">— Sélectionner —</option>
                                    <option value="M" @selected(old('sexe', $dossier->sexe) === 'M')>Masculin</option>
                                    <option value="F" @selected(old('sexe', $dossier->sexe) === 'F')>Féminin</option>
                                    <option value="Autre" @selected(old('sexe', $dossier->sexe) === 'Autre')>Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Groupe sanguin</label>
                                <select name="groupe_sanguin" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="">— Sélectionner —</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gs)
                                        <option value="{{ $gs }}" @selected(old('groupe_sanguin', $dossier->groupe_sanguin) === $gs)>{{ $gs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone', $dossier->telephone) }}"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse</label>
                                <input type="text" name="adresse" value="{{ old('adresse', $dossier->adresse) }}"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== INFORMATIONS MÉDICALES ===== --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Informations médicales</h4>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Allergies</label>
                            <textarea name="allergies" rows="2" placeholder="Ex: pénicilline, arachides..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none">{{ old('allergies', $dossier->allergies) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Maladies chroniques</label>
                            <textarea name="maladies_chroniques" rows="2" placeholder="Ex: diabète, hypertension..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none">{{ old('maladies_chroniques', $dossier->maladies_chroniques) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Traitements en cours</label>
                            <textarea name="traitements_en_cours" rows="2" placeholder="Médicaments / traitements actuels..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none">{{ old('traitements_en_cours', $dossier->traitements_en_cours) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antécédents familiaux</label>
                                <textarea name="antecedents_familiaux" rows="3" placeholder="Maladies héréditaires, antécédents familiaux..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none">{{ old('antecedents_familiaux', $dossier->antecedents_familiaux) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antécédents personnels</label>
                                <textarea name="antecedents_personnels" rows="3" placeholder="Chirurgies, hospitalisations passées..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none">{{ old('antecedents_personnels', $dossier->antecedents_personnels) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== CONTACT D'URGENCE ===== --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/10">
                        <h4 class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">🚨 Contact d'urgence</h4>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                                <input type="text" name="contact_urgence_nom" value="{{ old('contact_urgence_nom', $dossier->contact_urgence_nom) }}" placeholder="Nom du contact"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-400 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                                <input type="text" name="contact_urgence_telephone" value="{{ old('contact_urgence_telephone', $dossier->contact_urgence_telephone) }}" placeholder="+243 000 000 000"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-400 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Relation</label>
                                <input type="text" name="contact_urgence_relation" value="{{ old('contact_urgence_relation', $dossier->contact_urgence_relation) }}" placeholder="Ex: Père, Épouse, Ami..."
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-400 focus:border-transparent text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== PIÈCE D'IDENTITÉ ===== --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Pièce d'identité</h4>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de pièce</label>
                                <select name="type_piece_identite" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="">— Sélectionner —</option>
                                    <option value="cni" @selected(old('type_piece_identite', $dossier->type_piece_identite) === 'cni')>Carte Nationale d'Identité</option>
                                    <option value="passeport" @selected(old('type_piece_identite', $dossier->type_piece_identite) === 'passeport')>Passeport</option>
                                    <option value="permis" @selected(old('type_piece_identite', $dossier->type_piece_identite) === 'permis')>Permis de conduire</option>
                                    <option value="autre" @selected(old('type_piece_identite', $dossier->type_piece_identite) === 'autre')>Autre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de pièce</label>
                                <input type="text" name="numero_piece_identite" value="{{ old('numero_piece_identite', $dossier->numero_piece_identite) }}" placeholder="N° de la pièce"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'expiration</label>
                                <input type="date" name="date_expiration_piece_identite" value="{{ old('date_expiration_piece_identite', optional($dossier->date_expiration_piece_identite)->format('Y-m-d')) }}"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        {{-- Recto / Verso upload --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recto</label>
                                @if($dossier->piece_identite_recto_path)
                                    <img src="{{ Storage::url($dossier->piece_identite_recto_path) }}" alt="Recto"
                                        class="mb-2 w-40 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                @endif
                                <div class="flex items-center gap-3">
                                    <label for="recto_input" class="cursor-pointer flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-blue-400 dark:border-blue-500 text-blue-600 dark:text-blue-400 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $dossier->piece_identite_recto_path ? 'Remplacer' : 'Téléverser' }}
                                    </label>
                                    <span id="recto_name" class="text-xs text-gray-500 dark:text-gray-400 truncate"></span>
                                </div>
                                <img id="recto_new_preview" src="" alt="Nouveau recto" class="hidden mt-2 w-40 h-24 object-cover rounded-lg border border-blue-300 dark:border-blue-700">
                                <input id="recto_input" type="file" name="piece_identite_recto" accept="image/*" class="hidden" onchange="previewIdentityPiece(this, 'recto_name', 'recto_new_preview')">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Verso</label>
                                @if($dossier->piece_identite_verso_path)
                                    <img src="{{ Storage::url($dossier->piece_identite_verso_path) }}" alt="Verso"
                                        class="mb-2 w-40 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                @endif
                                <div class="flex items-center gap-3">
                                    <label for="verso_input" class="cursor-pointer flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-blue-400 dark:border-blue-500 text-blue-600 dark:text-blue-400 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $dossier->piece_identite_verso_path ? 'Remplacer' : 'Téléverser' }}
                                    </label>
                                    <span id="verso_name" class="text-xs text-gray-500 dark:text-gray-400 truncate"></span>
                                </div>
                                <img id="verso_new_preview" src="" alt="Nouveau verso" class="hidden mt-2 w-40 h-24 object-cover rounded-lg border border-blue-300 dark:border-blue-700">
                                <input id="verso_input" type="file" name="piece_identite_verso" accept="image/*" class="hidden" onchange="previewIdentityPiece(this, 'verso_name', 'verso_new_preview')">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== ACTIONS ===== --}}
                <div class="flex justify-between items-center pb-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Retour au tableau de bord
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white text-sm font-semibold shadow-md transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    preview.classList.add('block');
                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewIdentityPiece(input, nameId, previewId) {
            const name = document.getElementById(nameId);
            const preview = document.getElementById(previewId);
            const file = input.files?.[0];

            if (!file) {
                if (name) name.textContent = '';
                if (preview) {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
                return;
            }

            if (name) {
                name.textContent = file.name;
            }

            if (file.type.startsWith('image/') && preview) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>
