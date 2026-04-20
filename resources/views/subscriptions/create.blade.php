<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Réabonnement / Souscription Mensuelle') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Section Recherche Client -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Rechercher un Client
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                        Recherchez le client par numéro de dossier, nom ou prénom pour effectuer un réabonnement.
                    </p>

                    <div class="flex gap-4">
                        <div class="flex-1 relative">
                            <input type="text" id="searchQuery" placeholder="Tapez le N° dossier, nom ou prénom..."
                                   class="w-full px-4 py-3 pl-12 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                   autocomplete="off">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="button" id="searchBtn"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl transition duration-200 flex items-center justify-center font-medium shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Rechercher
                        </button>
                    </div>

                    <!-- Résultats de recherche -->
                    <div id="searchResults" class="mt-4 hidden">
                        <div id="resultsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <!-- Résultats injectés ici -->
                        </div>
                    </div>

                    <!-- Loader -->
                    <div id="searchLoader" class="hidden text-center py-6">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Recherche en cours...</p>
                    </div>

                    <!-- Aucun résultat -->
                    <div id="noResults" class="hidden text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Aucun client trouvé.</p>
                    </div>
                </div>
            </div>

            <!-- Section Client Sélectionné + Formulaire -->
            <div id="subscriptionSection" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informations Client -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                            <h4 class="text-white font-semibold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Client Sélectionné
                            </h4>
                        </div>
                        <div class="p-6">
                            <!-- Photo et infos -->
                            <div class="flex justify-center mb-4">
                                <div id="clientPhoto" class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center overflow-hidden shadow-lg">
                                    <span class="text-xl font-bold text-white">--</span>
                                </div>
                            </div>
                            <div id="clientInfo" class="text-center space-y-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">--</h3>
                                <p class="text-blue-600 dark:text-blue-400 font-mono">--</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">📞 --</p>
                            </div>

                            <!-- Statut abonnement -->
                            <div id="subscriptionStatus" class="mt-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700">
                                <p class="text-center text-gray-500 dark:text-gray-400">Sélectionnez un client</p>
                            </div>

                            <!-- Bouton changer de client -->
                            <div class="mt-4">
                                <button type="button" id="changeClientBtn"
                                        class="w-full text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Changer de client
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de Réabonnement -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h4 class="text-white font-semibold text-lg flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Formulaire de Réabonnement
                            </h4>
                        </div>
                        <div class="p-6">
                            <form id="subscriptionForm">
                                <input type="hidden" id="dossier_medical_id" name="dossier_medical_id">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Type d'abonnement -->
                                    <div>
                                        <label for="frais_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Type d'abonnement <span class="text-red-500">*</span>
                                        </label>
                                        <select id="frais_id" name="frais_id"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            @foreach($fraisReabonnement as $frais)
                                                <option value="{{ $frais->id }}" data-prix="{{ $frais->prix }}" {{ $loop->first ? 'selected' : '' }}>
                                                    {{ $frais->libelle }} - {{ number_format($frais->prix, 0, ',', ' ') }} XAF/mois
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Nombre de mois -->
                                    <div>
                                        <label for="nombre_mois" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Durée (mois) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-4 gap-2">
                                            @for($i = 1; $i <= 12; $i++)
                                                <button type="button" data-mois="{{ $i }}"
                                                        class="mois-btn px-3 py-2 text-sm font-medium rounded-lg border-2 transition-all duration-200
                                                               {{ $i == 1 ? 'border-purple-500 bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-purple-300' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" id="nombre_mois" name="nombre_mois" value="1">
                                    </div>

                                    <!-- Mode de paiement -->
                                    <div>
                                        <label for="mode_paiement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Mode de paiement <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="mode-paiement-option cursor-pointer">
                                                <input type="radio" name="mode_paiement" value="especes" class="hidden" checked>
                                                <div class="p-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-center transition-all duration-200 hover:border-purple-300 selected:border-purple-500 selected:bg-purple-50">
                                                    <span class="text-2xl">💵</span>
                                                    <p class="text-sm font-medium mt-1">Espèces</p>
                                                </div>
                                            </label>
                                            <label class="mode-paiement-option cursor-pointer">
                                                <input type="radio" name="mode_paiement" value="mobile_money" class="hidden">
                                                <div class="p-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-center transition-all duration-200 hover:border-purple-300">
                                                    <span class="text-2xl">📱</span>
                                                    <p class="text-sm font-medium mt-1">Mobile Money</p>
                                                </div>
                                            </label>
                                            <label class="mode-paiement-option cursor-pointer">
                                                <input type="radio" name="mode_paiement" value="carte_bancaire" class="hidden">
                                                <div class="p-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-center transition-all duration-200 hover:border-purple-300">
                                                    <span class="text-2xl">💳</span>
                                                    <p class="text-sm font-medium mt-1">Carte</p>
                                                </div>
                                            </label>
                                            <label class="mode-paiement-option cursor-pointer">
                                                <input type="radio" name="mode_paiement" value="virement" class="hidden">
                                                <div class="p-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-center transition-all duration-200 hover:border-purple-300">
                                                    <span class="text-2xl">🏦</span>
                                                    <p class="text-sm font-medium mt-1">Virement</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Référence -->
                                    <div>
                                        <label for="reference_paiement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Référence transaction
                                        </label>
                                        <input type="text" id="reference_paiement" name="reference_paiement"
                                               placeholder="N° reçu, transaction..."
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Résumé du calcul -->
                                <div id="calculSummary" class="mt-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border border-purple-200 dark:border-purple-800">
                                    <h5 class="text-sm font-semibold text-purple-800 dark:text-purple-300 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        RÉCAPITULATIF
                                    </h5>
                                    <div class="grid grid-cols-3 gap-6 text-center">
                                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date début</p>
                                            <p id="dateDebut" class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">--/--/----</p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date fin</p>
                                            <p id="dateFin" class="text-lg font-bold text-pink-600 dark:text-pink-400 mt-1">--/--/----</p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Montant total</p>
                                            <p id="montantTotal" class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">0 XAF</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mt-6">
                                    <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Notes / Remarques
                                    </label>
                                    <textarea id="notes" name="notes" rows="2"
                                              placeholder="Observations éventuelles..."
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                                </div>

                                <!-- Bouton de confirmation -->
                                <div class="mt-8">
                                    <button type="submit" id="submitBtn"
                                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-8 py-4 rounded-xl transition duration-200 flex items-center justify-center font-bold text-lg shadow-xl transform hover:scale-[1.02]">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Confirmer le Réabonnement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message de succès -->
            <div id="successMessage" class="hidden mt-6">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-emerald-800 dark:text-emerald-300 mb-2">Réabonnement Enregistré !</h3>
                    <p id="successDetails" class="text-emerald-600 dark:text-emerald-400 mb-4"></p>
                    <button type="button" id="newSubscriptionBtn"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Nouveau Réabonnement
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchQuery = document.getElementById('searchQuery');
            const searchBtn = document.getElementById('searchBtn');
            const searchResults = document.getElementById('searchResults');
            const resultsContainer = document.getElementById('resultsContainer');
            const searchLoader = document.getElementById('searchLoader');
            const noResults = document.getElementById('noResults');
            const subscriptionSection = document.getElementById('subscriptionSection');
            const successMessage = document.getElementById('successMessage');

            let selectedDossier = null;
            let selectedMois = 1;
            let searchTimeout = null;

            // Recherche automatique avec debounce
            function performSearch() {
                const query = searchQuery.value.trim();
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    noResults.classList.add('hidden');
                    return;
                }

                searchLoader.classList.remove('hidden');
                searchResults.classList.add('hidden');
                noResults.classList.add('hidden');

                fetch(`{{ route('subscriptions.search') }}?query=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    searchLoader.classList.add('hidden');

                    if (!data.dossiers || data.dossiers.length === 0) {
                        noResults.classList.remove('hidden');
                        return;
                    }

                    resultsContainer.innerHTML = data.dossiers.map(d => createClientCard(d)).join('');
                    searchResults.classList.remove('hidden');

                    // Event listeners pour sélection
                    document.querySelectorAll('.select-client-btn').forEach(btn => {
                        btn.addEventListener('click', () => selectClient(JSON.parse(btn.dataset.client)));
                    });
                })
                .catch(err => {
                    searchLoader.classList.add('hidden');
                    console.error(err);
                    alert('Erreur lors de la recherche.');
                });
            }

            // Recherche automatique à la saisie (debounce 300ms)
            searchQuery.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 300);
            });

            function createClientCard(dossier) {
                const statusClass = dossier.est_actif
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'
                    : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
                const statusText = dossier.est_actif ? `✓ Actif (${dossier.jours_restants}j)` : '⚠ Expiré';

                const initials = (dossier.prenom?.charAt(0) || '') + (dossier.nom?.charAt(0) || '');
                const photoHtml = dossier.photo_profil_path
                    ? `<img src="/storage/${dossier.photo_profil_path}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">`
                    : `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">${initials}</div>`;

                return `
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 border border-gray-200 dark:border-gray-600 hover:shadow-lg transition duration-200">
                        <div class="flex items-center space-x-3">
                            ${photoHtml}
                            <div class="flex-1 min-w-0">
                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 truncate">${dossier.prenom} ${dossier.nom}</h5>
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-mono">${dossier.numero_unique}</p>
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full ${statusClass}">${statusText}</span>
                            </div>
                            <button type="button" class="select-client-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium"
                                    data-client='${JSON.stringify(dossier)}'>
                                Sélectionner
                            </button>
                        </div>
                    </div>
                `;
            }

            function selectClient(dossier) {
                selectedDossier = dossier;
                document.getElementById('dossier_medical_id').value = dossier.id;

                // Mise à jour de l'affichage client
                const initials = (dossier.prenom?.charAt(0) || '') + (dossier.nom?.charAt(0) || '');
                document.getElementById('clientPhoto').innerHTML = dossier.photo_profil_path
                    ? `<img src="/storage/${dossier.photo_profil_path}" class="w-16 h-16 rounded-full object-cover">`
                    : `<span class="text-xl font-bold text-white">${initials}</span>`;

                document.getElementById('clientInfo').innerHTML = `
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">${dossier.prenom} ${dossier.nom}</h3>
                    <p class="text-blue-600 dark:text-blue-400 font-mono font-semibold">${dossier.numero_unique}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">📞 ${dossier.telephone || 'N/A'}</p>
                `;

                // Statut abonnement
                const statusContainer = document.getElementById('subscriptionStatus');
                if (dossier.est_actif) {
                    statusContainer.className = 'mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800';
                    statusContainer.innerHTML = `
                        <div class="text-center">
                            <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">✓ ACTIF</span>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Expire le ${dossier.date_fin_abonnement}</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">${dossier.jours_restants} jours restants</p>
                        </div>
                    `;
                } else {
                    statusContainer.className = 'mt-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800';
                    statusContainer.innerHTML = `
                        <div class="text-center">
                            <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">⚠ EXPIRÉ</span>
                            <p class="text-sm text-amber-600 dark:text-amber-400 mt-2">Dernier abonnement: ${dossier.date_fin_abonnement || 'Aucun'}</p>
                        </div>
                    `;
                }

                // Masquer recherche, afficher formulaire
                searchResults.classList.add('hidden');
                subscriptionSection.classList.remove('hidden');
                successMessage.classList.add('hidden');

                // Calculer les dates
                updateCalcul();
            }

            // Gestion des boutons de mois
            document.querySelectorAll('.mois-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.mois-btn').forEach(b => {
                        b.classList.remove('border-purple-500', 'bg-purple-50', 'text-purple-700', 'dark:bg-purple-900/30', 'dark:text-purple-300');
                        b.classList.add('border-gray-200', 'dark:border-gray-600', 'text-gray-600', 'dark:text-gray-400');
                    });
                    this.classList.remove('border-gray-200', 'dark:border-gray-600', 'text-gray-600', 'dark:text-gray-400');
                    this.classList.add('border-purple-500', 'bg-purple-50', 'text-purple-700', 'dark:bg-purple-900/30', 'dark:text-purple-300');

                    selectedMois = parseInt(this.dataset.mois);
                    document.getElementById('nombre_mois').value = selectedMois;
                    updateCalcul();
                });
            });

            // Mode de paiement
            document.querySelectorAll('.mode-paiement-option input').forEach(input => {
                input.addEventListener('change', function() {
                    document.querySelectorAll('.mode-paiement-option div').forEach(div => {
                        div.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/30');
                        div.classList.add('border-gray-200', 'dark:border-gray-600');
                    });
                    this.nextElementSibling.classList.remove('border-gray-200', 'dark:border-gray-600');
                    this.nextElementSibling.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/30');
                });
            });

            // Initialiser le mode de paiement sélectionné
            document.querySelector('.mode-paiement-option input:checked').nextElementSibling.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/30');
            document.querySelector('.mode-paiement-option input:checked').nextElementSibling.classList.remove('border-gray-200', 'dark:border-gray-600');

            // Calcul des dates et montant
            document.getElementById('frais_id').addEventListener('change', updateCalcul);

            function updateCalcul() {
                const dossierId = document.getElementById('dossier_medical_id').value;
                const fraisId = document.getElementById('frais_id').value;
                const nombreMois = document.getElementById('nombre_mois').value;

                if (!dossierId || !fraisId) return;

                fetch(`{{ route('subscriptions.calculer') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ dossier_medical_id: dossierId, frais_id: fraisId, nombre_mois: nombreMois })
                })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('dateDebut').textContent = data.date_debut_formatted;
                    document.getElementById('dateFin').textContent = data.date_fin_formatted;
                    document.getElementById('montantTotal').textContent = data.montant_formatted;
                })
                .catch(console.error);
            }

            // Soumission du formulaire
            document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const fraisId = document.getElementById('frais_id').value;
                if (!fraisId) {
                    alert('Veuillez sélectionner un type d\'abonnement.');
                    return;
                }

                const formData = {
                    dossier_medical_id: document.getElementById('dossier_medical_id').value,
                    frais_id: fraisId,
                    nombre_mois: document.getElementById('nombre_mois').value,
                    mode_paiement: document.querySelector('input[name="mode_paiement"]:checked').value,
                    reference_paiement: document.getElementById('reference_paiement').value,
                    notes: document.getElementById('notes').value
                };

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Traitement...';

                fetch(`{{ route('subscriptions.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(r => r.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Confirmer le Réabonnement';

                    if (data.success) {
                        subscriptionSection.classList.add('hidden');
                        successMessage.classList.remove('hidden');
                        document.getElementById('successDetails').textContent = data.message;
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Confirmer le Réabonnement';
                    console.error(err);
                    alert('Une erreur est survenue.');
                });
            });

            // Changer de client
            document.getElementById('changeClientBtn').addEventListener('click', function() {
                subscriptionSection.classList.add('hidden');
                searchResults.classList.remove('hidden');
                searchQuery.focus();
            });

            // Nouveau réabonnement
            document.getElementById('newSubscriptionBtn').addEventListener('click', function() {
                successMessage.classList.add('hidden');
                searchQuery.value = '';
                searchResults.classList.add('hidden');
                document.getElementById('subscriptionForm').reset();
                document.querySelectorAll('.mois-btn').forEach((b, i) => {
                    if (i === 0) {
                        b.classList.add('border-purple-500', 'bg-purple-50', 'text-purple-700');
                        b.classList.remove('border-gray-200', 'text-gray-600');
                    } else {
                        b.classList.remove('border-purple-500', 'bg-purple-50', 'text-purple-700');
                        b.classList.add('border-gray-200', 'text-gray-600');
                    }
                });
                selectedMois = 1;
                document.getElementById('nombre_mois').value = 1;
                searchQuery.focus();
            });

            // Events de recherche
            searchBtn.addEventListener('click', performSearch);
            searchQuery.addEventListener('keypress', e => { if (e.key === 'Enter') performSearch(); });
        });
    </script>
    @endpush
</x-app-layout>
