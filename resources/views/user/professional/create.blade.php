<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Devenir professionnel - Etape 1/2</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Formulaire utilisateur professionnel</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">Creation dossier professionnel utilisateur</h3>
                    <p class="text-emerald-100 text-sm mt-1">Vous paierez ensuite l'inscription, puis validation admin.</p>
                </div>

                <form method="POST" action="{{ route('user.professional.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Raison sociale</label>
                            <input type="text" name="raison_sociale" value="{{ old('raison_sociale') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('raison_sociale')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type structure *</label>
                            <select name="type_structure" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Selectionner</option>
                                <option value="individuel" @selected(old('type_structure') === 'individuel')>Individuel</option>
                                <option value="clinique" @selected(old('type_structure') === 'clinique')>Clinique</option>
                                <option value="hopital" @selected(old('type_structure') === 'hopital')>Hopital</option>
                                <option value="dispensaire" @selected(old('type_structure') === 'dispensaire')>Dispensaire</option>
                                <option value="autre" @selected(old('type_structure') === 'autre')>Autre</option>
                            </select>
                            @error('type_structure')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Spécialité *</label>
                        <select name="specialite" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Sélectionner une spécialité</option>
                            @foreach(['Médecine générale', 'Cardiologie', 'Pédiatrie', 'Gynécologie', 'Dermatologie', 'Dentisterie', 'Laboratoire', 'Radiologie', 'Kinésithérapie', 'Pharmacie', 'Ophtalmologie', 'ORL', 'Psychologie', 'Autre'] as $specialite)
                                <option value="{{ $specialite }}" @selected(old('specialite') === $specialite)>{{ $specialite }}</option>
                            @endforeach
                        </select>
                        @error('specialite')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIU</label>
                            <input type="text" name="NIU" value="{{ old('NIU') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Forme juridique</label>
                            <input type="text" name="forme_juridique" value="{{ old('forme_juridique') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label id="image_identite_label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de profil / Logo *</label>
                            <div class="rounded-xl border border-dashed border-emerald-300 dark:border-emerald-700 p-3 bg-emerald-50/60 dark:bg-emerald-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="image_identite_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir une image
                                    </label>
                                    <span id="image_identite_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="image_identite_input" type="file" name="image_identite" accept="image/*" required class="hidden" onchange="updateImageIdentitePreview(this)">
                                <img id="image_identite_preview" src="" alt="Prévisualisation" class="hidden mt-3 w-20 h-20 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                            </div>
                            @error('image_identite')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attestation professionnelle</label>
                            <div class="rounded-xl border border-dashed border-emerald-300 dark:border-emerald-700 p-3 bg-emerald-50/60 dark:bg-emerald-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="attestation_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir le fichier
                                    </label>
                                    <span id="attestation_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="attestation_input" type="file" name="attestation_professionnelle" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="updateDocumentPreview(this, 'attestation_name', 'attestation_preview', 'attestation_pdf_badge')">
                                <img id="attestation_preview" src="" alt="Prévisualisation attestation" class="hidden mt-3 w-40 h-24 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                                <span id="attestation_pdf_badge" class="hidden mt-3 w-fit items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-700">PDF prêt à envoyer</span>
                            </div>
                            @error('attestation_professionnelle')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document prise de fonction</label>
                            <div class="rounded-xl border border-dashed border-emerald-300 dark:border-emerald-700 p-3 bg-emerald-50/60 dark:bg-emerald-900/20">
                                <div class="flex items-center gap-3">
                                    <label for="prise_fonction_input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Choisir le fichier
                                    </label>
                                    <span id="prise_fonction_name" class="text-xs text-gray-600 dark:text-gray-300 truncate">Aucun fichier sélectionné</span>
                                </div>
                                <input id="prise_fonction_input" type="file" name="document_prise_de_fonction" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="updateDocumentPreview(this, 'prise_fonction_name', 'prise_fonction_preview', 'prise_fonction_pdf_badge')">
                                <img id="prise_fonction_preview" src="" alt="Prévisualisation document" class="hidden mt-3 w-40 h-24 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                                <span id="prise_fonction_pdf_badge" class="hidden mt-3 w-fit items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-700">PDF prêt à envoyer</span>
                            </div>
                            @error('document_prise_de_fonction')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-100 dark:border-emerald-900">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frais inscription professionnelle *</label>
                        <select name="frais_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Selectionner</option>
                            @foreach($fraisInscription as $frais)
                                <option value="{{ $frais->id }}" @selected(old('frais_id') == $frais->id)>
                                    {{ $frais->libelle }} - {{ number_format((float) $frais->prix, 0, ',', ' ') }} XAF
                                </option>
                            @endforeach
                        </select>
                        @error('frais_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Annuler</a>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium">
                            Continuer vers paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function refreshImageIdentiteLabel() {
            const typeStructure = document.querySelector('select[name="type_structure"]')?.value;
            const label = document.getElementById('image_identite_label');

            if (!label) {
                return;
            }

            label.textContent = typeStructure === 'individuel' ? 'Photo de profil *' : 'Logo de la structure *';
        }

        function updateImageIdentitePreview(input) {
            const name = document.getElementById('image_identite_name');
            const imagePreview = document.getElementById('image_identite_preview');
            const file = input.files?.[0];

            if (!file) {
                if (name) {
                    name.textContent = 'Aucun fichier sélectionné';
                }
                if (imagePreview) {
                    imagePreview.src = '';
                    imagePreview.classList.add('hidden');
                }
                return;
            }

            if (name) {
                name.textContent = file.name;
            }

            if (imagePreview && file.type.startsWith('image/')) {
                imagePreview.src = URL.createObjectURL(file);
                imagePreview.classList.remove('hidden');
            }
        }

        function updateDocumentPreview(input, nameId, imagePreviewId, pdfBadgeId) {
            const name = document.getElementById(nameId);
            const imagePreview = document.getElementById(imagePreviewId);
            const pdfBadge = document.getElementById(pdfBadgeId);
            const file = input.files?.[0];

            if (!file) {
                if (name) name.textContent = 'Aucun fichier sélectionné';
                if (imagePreview) {
                    imagePreview.src = '';
                    imagePreview.classList.add('hidden');
                }
                if (pdfBadge) {
                    pdfBadge.classList.add('hidden');
                }
                return;
            }

            if (name) {
                name.textContent = file.name;
            }

            if (file.type.startsWith('image/')) {
                if (imagePreview) {
                    imagePreview.src = URL.createObjectURL(file);
                    imagePreview.classList.remove('hidden');
                }
                if (pdfBadge) {
                    pdfBadge.classList.add('hidden');
                }
                return;
            }

            if (imagePreview) {
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
            }
            if (pdfBadge && file.type === 'application/pdf') {
                pdfBadge.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const typeStructureSelect = document.querySelector('select[name="type_structure"]');

            refreshImageIdentiteLabel();

            if (typeStructureSelect) {
                typeStructureSelect.addEventListener('change', refreshImageIdentiteLabel);
            }
        });
    </script>
    @endpush
</x-app-layout>
