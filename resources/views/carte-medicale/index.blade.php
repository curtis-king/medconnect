<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Génération de Carte Médicale') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Recherchez un client pour générer ou imprimer sa carte médicale PVC
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Section Recherche -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        Rechercher un Client
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                        Recherchez le client par numéro de dossier, nom, prénom ou téléphone pour générer sa carte médicale.
                    </p>

                    <div class="flex gap-4">
                        <div class="flex-1 relative">
                            <input type="text" id="searchQuery" placeholder="Tapez le N° dossier, nom, prénom ou téléphone..."
                                   class="w-full px-4 py-3 pl-12 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                                   autocomplete="off">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="button" id="searchBtn"
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl transition duration-200 flex items-center justify-center font-medium shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Rechercher
                        </button>
                    </div>

                    <!-- Résultats de recherche -->
                    <div id="searchResults" class="mt-6 hidden">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Résultats :</h4>
                        <div id="resultsContainer" class="space-y-3">
                            <!-- Résultats injectés ici -->
                        </div>
                    </div>

                    <!-- Loader -->
                    <div id="searchLoader" class="hidden text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent"></div>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Recherche en cours...</p>
                    </div>

                    <!-- Aucun résultat -->
                    <div id="noResults" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Aucun client trouvé avec ces critères.</p>
                    </div>
                </div>
            </div>

            <!-- Informations carte PVC -->
            <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 border border-indigo-200 dark:border-gray-600">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-indigo-800 dark:text-indigo-300">
                            Format Carte PVC Standard
                        </h4>
                        <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-400">
                            Les cartes sont générées au format carte de crédit standard (85.60 × 53.98 mm) avec recto et verso.
                            Parfait pour impression sur imprimante PVC ou plastification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchQuery');
            const searchBtn = document.getElementById('searchBtn');
            const searchResults = document.getElementById('searchResults');
            const resultsContainer = document.getElementById('resultsContainer');
            const searchLoader = document.getElementById('searchLoader');
            const noResults = document.getElementById('noResults');

            let debounceTimer;

            function doSearch() {
                const query = searchInput.value.trim();

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    noResults.classList.add('hidden');
                    return;
                }

                searchLoader.classList.remove('hidden');
                searchResults.classList.add('hidden');
                noResults.classList.add('hidden');

                fetch(`{{ route('carte-medicale.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoader.classList.add('hidden');

                        if (data.length === 0) {
                            noResults.classList.remove('hidden');
                            return;
                        }

                        resultsContainer.innerHTML = data.map(dossier => `
                            <div class="bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 p-4 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        ${dossier.photo_profil_path
                                            ? `<img src="/storage/${dossier.photo_profil_path}" alt="Photo" class="w-10 h-10 rounded-full object-cover border-2 border-indigo-200">`
                                            : `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                                                ${(dossier.prenom?.[0] || '').toUpperCase()}${(dossier.nom?.[0] || '').toUpperCase()}
                                               </div>`
                                        }
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">${dossier.prenom} ${dossier.nom}</p>
                                            <p class="text-sm text-indigo-600 dark:text-indigo-400 font-mono">${dossier.numero_unique}</p>
                                            <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                ${dossier.telephone ? `<span>📞 ${dossier.telephone}</span>` : ''}
                                                ${dossier.groupe_sanguin ? `<span class="px-2 py-0.5 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200 rounded-full">🩸 ${dossier.groupe_sanguin}</span>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="/carte-medicale/${dossier.id}/demande"
                                           class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Voir
                                        </a>
                                        <a href="/carte-medicale/${dossier.id}/generer"
                                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                            </svg>
                                            Générer Carte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        searchResults.classList.remove('hidden');
                    })
                    .catch(error => {
                        searchLoader.classList.add('hidden');
                        console.error('Erreur:', error);
                    });
            }

            searchBtn.addEventListener('click', doSearch);

            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    doSearch();
                } else {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(doSearch, 400);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
