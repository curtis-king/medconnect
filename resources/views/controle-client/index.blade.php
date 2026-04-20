<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Contrôle et Renseignement Client') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Section Recherche -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        🔍 Rechercher un Client
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                        Recherchez par numéro de dossier, nom ou prénom du patient.
                    </p>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" id="searchQuery" placeholder="N° Dossier, Nom ou Prénom..."
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg">
                        </div>
                        <button type="button" id="searchBtn"
                                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-xl transition duration-200 flex items-center justify-center font-medium shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Rechercher
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loader -->
            <div id="loader" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                <p class="mt-3 text-gray-600 dark:text-gray-400 text-lg">Recherche en cours...</p>
            </div>

            <!-- Résultats de recherche -->
            <div id="results" class="hidden mb-6">
                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    📋 Résultats de recherche
                </h4>
                <div id="resultsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Les résultats seront injectés ici -->
                </div>
            </div>

            <!-- Aucun résultat -->
            <div id="noResults" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-8 text-center border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-gray-600 dark:text-gray-400 text-lg">Aucun résultat trouvé.</p>
                <p class="mt-2 text-gray-500 dark:text-gray-500 text-sm">Essayez avec un autre terme de recherche.</p>
            </div>

            <!-- Détails Client + Formulaire d'abonnement -->
            <div id="clientDetails" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations Client -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                            <h4 class="text-white font-semibold text-lg">👤 Informations Client</h4>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-center mb-4">
                                <div id="clientPhoto" class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div id="clientInfo" class="space-y-3 text-center">
                                <!-- Client info sera injecté ici -->
                            </div>
                            <div id="subscriptionStatus" class="mt-6 p-4 rounded-xl">
                                <!-- Statut abonnement -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglets: Abonnement | Pièces d'Identité -->
                <div class="lg:col-span-2">
                    <!-- Barre d'onglets -->
                    <div class="flex gap-2 mb-5">
                        <button id="tabAbonnementBtn" onclick="switchTab('abonnement')"
                                class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-emerald-600 text-white shadow">
                            💳 Abonnement
                        </button>
                        <button id="tabPiecesBtn" onclick="switchTab('pieces')"
                                class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            🪪 Pièces d'Identité
                        </button>
                    </div>

                    <!-- Panneau: Abonnement -->
                    <div id="panelAbonnement">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                            <h4 class="text-white font-semibold text-lg">💳 Paiement Abonnement Mensuel</h4>
                        </div>
                        <div class="p-6">
                            <form id="subscriptionForm">
                                <input type="hidden" id="dossier_medical_id" name="dossier_medical_id">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Frais de réabonnement -->
                                    <div>
                                        <label for="frais_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Type d'abonnement
                                        </label>
                                        <select id="frais_id" name="frais_id"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <option value="">Sélectionner un type</option>
                                            @foreach($fraisReabonnement as $frais)
                                                <option value="{{ $frais->id }}" data-prix="{{ $frais->prix }}">
                                                    {{ $frais->libelle }} - {{ number_format($frais->prix, 0, ',', ' ') }} XAF/mois
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Nombre de mois -->
                                    <div>
                                        <label for="nombre_mois" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Nombre de mois
                                        </label>
                                        <select id="nombre_mois" name="nombre_mois"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ $i }} mois</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Mode de paiement -->
                                    <div>
                                        <label for="mode_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Mode de paiement
                                        </label>
                                        <select id="mode_paiement" name="mode_paiement"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <option value="especes">Espèces</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="carte_bancaire">Carte Bancaire</option>
                                            <option value="virement">Virement</option>
                                        </select>
                                    </div>

                                    <!-- Référence -->
                                    <div>
                                        <label for="reference_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Référence (optionnel)
                                        </label>
                                        <input type="text" id="reference_paiement" name="reference_paiement" placeholder="N° transaction..."
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Calcul automatique -->
                                <div id="calculSection" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Date de début</p>
                                            <p id="dateDebut" class="text-lg font-bold text-blue-600 dark:text-blue-400">--/--/----</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Date de fin</p>
                                            <p id="dateFin" class="text-lg font-bold text-indigo-600 dark:text-indigo-400">--/--/----</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Montant total</p>
                                            <p id="montantTotal" class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">0 XAF</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mt-6">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Notes (optionnel)
                                    </label>
                                    <textarea id="notes" name="notes" rows="2" placeholder="Remarques éventuelles..."
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"></textarea>
                                </div>

                                <!-- Bouton de paiement -->
                                <div class="mt-6">
                                    <button type="submit" id="payBtn"
                                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-4 rounded-xl transition duration-200 flex items-center justify-center font-semibold text-lg shadow-lg">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Confirmer le Paiement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Historique des abonnements -->
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h4 class="text-white font-semibold text-lg">📜 Historique des Abonnements</h4>
                        </div>
                        <div class="p-6">
                            <div id="subscriptionHistory" class="space-y-3">
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun historique disponible</p>
                            </div>
                        </div>
                    </div>
                    </div>{{-- /panelAbonnement --}}

                    <!-- Panneau: Pièces d'Identité -->
                    <div id="panelPieces" class="hidden">

                        <!-- Chargement -->
                        <div id="pieceLoader" class="hidden text-center py-10">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
                            <p class="mt-3 text-gray-500 dark:text-gray-400 text-sm">Chargement des pièces...</p>
                        </div>

                        <!-- Contenu des pièces -->
                        <div id="pieceContent" class="hidden space-y-5">

                            <!-- Pièce d'identité: métadonnées + images -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                                    <h4 class="text-white font-semibold text-lg">🪪 Pièce d'Identité</h4>
                                </div>
                                <div class="p-6">
                                    <div id="pieceMetadata" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Recto</p>
                                            <div id="pieceRecto" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl min-h-[160px] flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden p-2">
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Verso</p>
                                            <div id="pieceVerso" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl min-h-[160px] flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden p-2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Analyse IA -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-4">
                                    <h4 class="text-white font-semibold text-lg">🤖 Analyse IA de Conformité</h4>
                                </div>
                                <div class="p-6">
                                    <div id="iaAnalysis"></div>
                                </div>
                            </div>

                            <!-- Décision du personnel -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                                    <h4 class="text-white font-semibold text-lg">✅ Décision du Personnel</h4>
                                </div>
                                <div class="p-6">
                                    <div id="existingDecision" class="hidden mb-6 p-4 rounded-xl border"></div>
                                    <form id="pieceValidationForm" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Décision</label>
                                                <select id="pieceDecision" name="decision" required
                                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                                    <option value="">Sélectionner...</option>
                                                    <option value="valide">✓ Valider les pièces</option>
                                                    <option value="rejete">✗ Rejeter les pièces</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note (optionnel)</label>
                                                <input type="text" id="pieceNote" name="note_personnel"
                                                       placeholder="Motif, observation..."
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                            </div>
                                        </div>
                                        <button type="submit" id="pieceValidateBtn"
                                                class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-6 py-3.5 rounded-xl transition duration-200 flex items-center justify-center font-semibold text-base shadow-lg">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Enregistrer la Décision
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>

                        <!-- État vide -->
                        <div id="pieceEmpty" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-gray-600 dark:text-gray-400">Sélectionnez un client pour consulter et valider ses pièces d'identité.</p>
                        </div>

                    </div>{{-- /panelPieces --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBtn = document.getElementById('searchBtn');
            const searchQuery = document.getElementById('searchQuery');
            const loader = document.getElementById('loader');
            const results = document.getElementById('results');
            const noResults = document.getElementById('noResults');
            const resultsContainer = document.getElementById('resultsContainer');
            const clientDetails = document.getElementById('clientDetails');
            const queryParams = new URLSearchParams(window.location.search);

            let selectedDossierId = null;

            // Recherche
            function performSearch() {
                const query = searchQuery.value.trim();

                if (!query) {
                    alert('Veuillez saisir un critère de recherche.');
                    return;
                }

                loader.classList.remove('hidden');
                results.classList.add('hidden');
                noResults.classList.add('hidden');
                clientDetails.classList.add('hidden');

                fetch(`{{ route('controle-client.search') }}?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loader.classList.add('hidden');

                    if (!data.dossiers || data.dossiers.length === 0) {
                        noResults.classList.remove('hidden');
                        return;
                    }

                    resultsContainer.innerHTML = '';
                    data.dossiers.forEach(dossier => {
                        const card = createDossierCard(dossier);
                        resultsContainer.innerHTML += card;
                    });
                    results.classList.remove('hidden');

                    // Ajouter les event listeners aux boutons
                    document.querySelectorAll('.select-client-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            selectClient(this.dataset.id);
                        });
                    });
                })
                .catch(error => {
                    loader.classList.add('hidden');
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la recherche.');
                });
            }

            function createDossierCard(dossier) {
                const statusClass = dossier.est_actif
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800';

                const statusText = dossier.est_actif ? '✓ Actif' : '✗ Expiré';

                const photoHtml = dossier.photo_profil_path
                    ? `<img src="/storage/${dossier.photo_profil_path}" alt="Photo" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow">`
                    : `<div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-lg shadow">
                         ${dossier.prenom?.charAt(0) || ''}${dossier.nom?.charAt(0) || ''}
                       </div>`;

                const joursRestants = dossier.subscription_active
                    ? `<span class="text-xs text-gray-500 dark:text-gray-400">(${dossier.subscription_active.jours_restants} jours restants)</span>`
                    : '';

                return `
                    <div class="bg-white dark:bg-gray-700 rounded-xl p-5 border border-gray-200 dark:border-gray-600 hover:shadow-xl transition duration-300 cursor-pointer transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            ${photoHtml}
                            <div class="flex-1 min-w-0">
                                <h5 class="text-md font-bold text-gray-900 dark:text-gray-100 truncate">
                                    ${dossier.prenom} ${dossier.nom}
                                </h5>
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-mono">
                                    ${dossier.numero_unique}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    📞 ${dossier.telephone || 'N/A'}
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full border ${statusClass}">
                                        ${statusText}
                                    </span>
                                    ${joursRestants}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="button" data-id="${dossier.id}"
                                    class="select-client-btn w-full inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-lg transition duration-200 shadow">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Sélectionner
                            </button>
                        </div>
                    </div>
                `;
            }

            // Sélectionner un client
            function selectClient(dossierId) {
                selectedDossierId = dossierId;

                loader.classList.remove('hidden');
                clientDetails.classList.add('hidden');

                fetch(`{{ url('controle-client') }}/${dossierId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loader.classList.add('hidden');
                    displayClientDetails(data);
                    clientDetails.classList.remove('hidden');

                    // Scroll vers les détails
                    clientDetails.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    loadDocuments(dossierId);
                })
                .catch(error => {
                    loader.classList.add('hidden');
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors du chargement des détails.');
                });
            }

            // Afficher les détails du client
            function displayClientDetails(data) {
                document.getElementById('dossier_medical_id').value = data.dossier.id;

                // Photo
                const photoContainer = document.getElementById('clientPhoto');
                if (data.dossier.photo_profil_path) {
                    photoContainer.innerHTML = `<img src="/storage/${data.dossier.photo_profil_path}" alt="Photo" class="w-full h-full object-cover">`;
                } else {
                    photoContainer.innerHTML = `
                        <span class="text-3xl font-bold text-white bg-gradient-to-br from-blue-400 to-indigo-500 w-full h-full flex items-center justify-center">
                            ${data.dossier.prenom?.charAt(0) || ''}${data.dossier.nom?.charAt(0) || ''}
                        </span>
                    `;
                }

                // Infos client
                document.getElementById('clientInfo').innerHTML = `
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">${data.dossier.prenom} ${data.dossier.nom}</h3>
                    <p class="text-blue-600 dark:text-blue-400 font-mono font-semibold">${data.dossier.numero_unique}</p>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 mt-3 text-left">
                        <p>📞 ${data.dossier.telephone || 'N/A'}</p>
                        <p>🎂 ${data.dossier.date_naissance || 'N/A'}</p>
                        <p>⚧ ${data.dossier.sexe === 'M' ? 'Masculin' : data.dossier.sexe === 'F' ? 'Féminin' : 'N/A'}</p>
                        <p>📍 ${data.dossier.adresse || 'N/A'}</p>
                    </div>
                `;

                // Statut abonnement
                const statusContainer = document.getElementById('subscriptionStatus');
                if (data.est_actif && data.subscription_active) {
                    statusContainer.className = 'mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800';
                    statusContainer.innerHTML = `
                        <div class="text-center">
                            <span class="inline-flex px-4 py-2 text-sm font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                ✓ ABONNEMENT ACTIF
                            </span>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                Valide jusqu'au <strong class="text-emerald-600 dark:text-emerald-400">${data.subscription_active.date_fin}</strong>
                            </p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                                ${data.subscription_active.jours_restants} jours restants
                            </p>
                        </div>
                    `;
                } else {
                    statusContainer.className = 'mt-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
                    statusContainer.innerHTML = `
                        <div class="text-center">
                            <span class="inline-flex px-4 py-2 text-sm font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                ✗ ABONNEMENT EXPIRÉ
                            </span>
                            <p class="mt-3 text-sm text-red-600 dark:text-red-400">
                                Veuillez renouveler l'abonnement
                            </p>
                        </div>
                    `;
                }

                // Dates de calcul initiales
                document.getElementById('dateDebut').textContent = data.prochaine_date_debut;

                // Historique des abonnements
                const historyContainer = document.getElementById('subscriptionHistory');
                if (data.historique_subscriptions && data.historique_subscriptions.length > 0) {
                    historyContainer.innerHTML = data.historique_subscriptions.map(sub => {
                        const statusClass = sub.statut === 'actif' && !sub.statut_label.includes('Expiré')
                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'
                            : sub.statut === 'annule'
                                ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
                                : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';

                        return `
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            ${sub.date_debut} → ${sub.date_fin}
                                        </span>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full ${statusClass}">
                                            ${sub.statut_label}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        ${sub.nombre_mois} mois • ${sub.mode_paiement || 'N/A'} • Payé le ${sub.date_paiement || 'N/A'}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">${sub.montant}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    historyContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun abonnement enregistré</p>';
                }

                // Déclencher le calcul des frais
                updateCalcul();
            }

            // Calculer les dates et montant automatiquement
            function updateCalcul() {
                const dossierId = document.getElementById('dossier_medical_id').value;
                const fraisId = document.getElementById('frais_id').value;
                const nombreMois = document.getElementById('nombre_mois').value;

                if (!dossierId || !fraisId) {
                    return;
                }

                fetch(`{{ route('subscriptions.calculer') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dossier_medical_id: dossierId,
                        frais_id: fraisId,
                        nombre_mois: nombreMois
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('dateDebut').textContent = data.date_debut_formatted;
                    document.getElementById('dateFin').textContent = data.date_fin_formatted;
                    document.getElementById('montantTotal').textContent = data.montant_formatted;
                })
                .catch(error => {
                    console.error('Erreur calcul:', error);
                });
            }

            // Événements de calcul automatique
            document.getElementById('frais_id').addEventListener('change', updateCalcul);
            document.getElementById('nombre_mois').addEventListener('change', updateCalcul);

            // Soumission du formulaire
            document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = {
                    dossier_medical_id: document.getElementById('dossier_medical_id').value,
                    frais_id: document.getElementById('frais_id').value,
                    nombre_mois: document.getElementById('nombre_mois').value,
                    mode_paiement: document.getElementById('mode_paiement').value,
                    reference_paiement: document.getElementById('reference_paiement').value,
                    notes: document.getElementById('notes').value
                };

                if (!formData.frais_id) {
                    alert('Veuillez sélectionner un type d\'abonnement.');
                    return;
                }

                const payBtn = document.getElementById('payBtn');
                payBtn.disabled = true;
                payBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement en cours...';

                fetch(`{{ route('subscriptions.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Confirmer le Paiement';

                    if (data.success) {
                        alert('✓ ' + data.message);
                        // Recharger les détails du client
                        selectClient(selectedDossierId);
                        // Reset le formulaire
                        document.getElementById('reference_paiement').value = '';
                        document.getElementById('notes').value = '';
                    } else {
                        alert('✗ ' + data.message);
                    }
                })
                .catch(error => {
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Confirmer le Paiement';
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors du paiement.');
                });
            });

            // Événements de recherche
            searchBtn.addEventListener('click', performSearch);
            searchQuery.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            const preselectedDossierId = queryParams.get('dossier');
            const preselectedQuery = queryParams.get('query');

            if (preselectedQuery) {
                searchQuery.value = preselectedQuery;
            }

            if (preselectedDossierId) {
                selectClient(preselectedDossierId);
            } else if (preselectedQuery) {
                performSearch();
            }

            // Validation des pièces d'identité
            document.getElementById('pieceValidationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const dossierId = this.dataset.dossierId;
                const decision = document.getElementById('pieceDecision').value;
                const note = document.getElementById('pieceNote').value;

                if (!decision) {
                    alert('Veuillez sélectionner une décision.');
                    return;
                }

                const btn = document.getElementById('pieceValidateBtn');
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...';

                fetch(`/controle-client/${dossierId}/documents/validation`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ decision, note_personnel: note })
                })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Enregistrer la Décision';
                    alert('\u2713 ' + (data.message || 'Décision enregistrée.'));
                    loadDocuments(dossierId);
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Enregistrer la Décision';
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue.');
                });
            });
        });

        function switchTab(tab) {
            const abonnementPanel = document.getElementById('panelAbonnement');
            const piecesPanel = document.getElementById('panelPieces');
            const abonnementBtn = document.getElementById('tabAbonnementBtn');
            const piecesBtn = document.getElementById('tabPiecesBtn');

            if (tab === 'abonnement') {
                abonnementPanel.classList.remove('hidden');
                piecesPanel.classList.add('hidden');
                abonnementBtn.className = 'flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-emerald-600 text-white shadow';
                piecesBtn.className = 'flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
            } else {
                piecesPanel.classList.remove('hidden');
                abonnementPanel.classList.add('hidden');
                piecesBtn.className = 'flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-blue-600 text-white shadow';
                abonnementBtn.className = 'flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm transition duration-200 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
            }
        }

        function loadDocuments(dossierId) {
            const loader = document.getElementById('pieceLoader');
            const content = document.getElementById('pieceContent');
            const empty = document.getElementById('pieceEmpty');

            loader.classList.remove('hidden');
            content.classList.add('hidden');
            empty.classList.add('hidden');

            fetch(`/controle-client/${dossierId}/documents`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                loader.classList.add('hidden');
                renderDocuments(data);
                content.classList.remove('hidden');
                document.getElementById('pieceValidationForm').dataset.dossierId = dossierId;
            })
            .catch(error => {
                loader.classList.add('hidden');
                empty.classList.remove('hidden');
                console.error('Erreur chargement pièces:', error);
            });
        }

        function renderDocuments(data) {
            const doc = data.documents;
            const typesLabels = {
                carte_nationale: 'Carte Nationale',
                passeport: 'Passeport',
                permis_conduire: 'Permis de Conduire',
                autre: 'Autre'
            };

            document.getElementById('pieceMetadata').innerHTML = `
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Type</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">${typesLabels[doc.type_piece_identite] || doc.type_piece_identite || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Numéro</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100 font-mono">${doc.numero_piece_identite || 'N/A'}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Expiration</p>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">${doc.date_expiration_piece_identite || 'N/A'}</p>
                </div>
            `;

            const renderImg = (path, containerId) => {
                const el = document.getElementById(containerId);
                el.innerHTML = path
                    ? `<a href="/storage/${path}" target="_blank" rel="noopener noreferrer"><img src="/storage/${path}" alt="Pièce" class="max-w-full max-h-64 object-contain rounded-lg cursor-zoom-in hover:opacity-90 transition"></a>`
                    : `<p class="text-gray-400 dark:text-gray-500 text-sm text-center px-4">Aucun fichier fourni</p>`;
            };
            renderImg(doc.piece_identite_recto_path, 'pieceRecto');
            renderImg(doc.piece_identite_verso_path, 'pieceVerso');

            const v = data.validation;
            const riskColors = {
                low: 'text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800',
                medium: 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800',
                high: 'text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
            };
            const riskLabels = { low: 'Faible ✓', medium: 'Moyen ⚠', high: 'Élevé ✗' };
            const riskClass = riskColors[v.ia_risk_level] || riskColors.low;
            const reasons = Array.isArray(v.ia_reasons) && v.ia_reasons.length > 0
                ? `<ul class="mt-3 space-y-1 text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">${v.ia_reasons.map(r => `<li>${r}</li>`).join('')}</ul>`
                : '<p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Aucun signalement particulier.</p>';

            document.getElementById('iaAnalysis').innerHTML = `
                <div class="flex items-stretch gap-4 mb-4">
                    <div class="flex-1 text-center p-4 rounded-xl ${riskClass}">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-1">Niveau de risque</p>
                        <p class="text-2xl font-bold">${riskLabels[v.ia_risk_level] || v.ia_risk_level || 'N/A'}</p>
                    </div>
                    <div class="flex-1 text-center p-4 rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-1 text-gray-500 dark:text-gray-400">Score conformité</p>
                        <p class="text-2xl font-bold text-slate-700 dark:text-slate-200">${v.ia_score ?? 'N/A'}<span class="text-sm font-normal">/100</span></p>
                    </div>
                    <div class="flex-1 text-center p-4 rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-1 text-gray-500 dark:text-gray-400">Source</p>
                        <p class="text-xl font-bold text-slate-700 dark:text-slate-200">${v.source === 'ai' ? '🤖 IA' : '📋 Local'}</p>
                    </div>
                </div>
                ${reasons}
            `;

            const existingDiv = document.getElementById('existingDecision');
            if (v.statut && v.statut !== 'en_attente') {
                const isValide = v.statut === 'valide';
                existingDiv.className = `mb-6 p-4 rounded-xl border ${isValide ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'}`;
                existingDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">${isValide ? '\u2705' : '\u274C'}</span>
                        <div>
                            <p class="font-semibold ${isValide ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'}">
                                Décision actuelle : ${isValide ? 'Validé' : 'Rejeté'}
                            </p>
                            ${v.personnel_note ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Note : ${v.personnel_note}</p>` : ''}
                            ${v.personnel_validated_at ? `<p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Enregistré le ${v.personnel_validated_at}</p>` : ''}
                        </div>
                    </div>
                `;
                existingDiv.classList.remove('hidden');
                document.getElementById('pieceDecision').value = v.statut;
                if (v.personnel_note) { document.getElementById('pieceNote').value = v.personnel_note; }
            } else {
                existingDiv.classList.add('hidden');
                document.getElementById('pieceDecision').value = '';
                document.getElementById('pieceNote').value = '';
            }
        }
    </script>
    @endpush
</x-app-layout>
