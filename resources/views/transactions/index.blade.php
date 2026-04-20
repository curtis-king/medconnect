<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historique des Transactions') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filtres & Recherche
                    </h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <!-- Période rapide -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Période</label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" name="periode" value="jour"
                                            class="px-3 py-1.5 text-sm rounded-lg border-2 transition-all {{ $periode === 'jour' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        Aujourd'hui
                                    </button>
                                    <button type="submit" name="periode" value="semaine"
                                            class="px-3 py-1.5 text-sm rounded-lg border-2 transition-all {{ $periode === 'semaine' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        Semaine
                                    </button>
                                    <button type="submit" name="periode" value="mois"
                                            class="px-3 py-1.5 text-sm rounded-lg border-2 transition-all {{ $periode === 'mois' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        Mois
                                    </button>
                                    <button type="button" onclick="toggleCustomDates()"
                                            class="px-3 py-1.5 text-sm rounded-lg border-2 transition-all {{ $periode === 'personnalise' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        Personnalisé
                                    </button>
                                </div>
                            </div>

                            <!-- Date unique (jour) -->
                            <div id="singleDateContainer" class="{{ $periode !== 'jour' ? 'hidden' : '' }}">
                                <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                                <input type="date" id="date" name="date" value="{{ $date }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <!-- Dates personnalisées -->
                            <div id="customDatesContainer" class="{{ $periode !== 'personnalise' ? 'hidden' : '' }} col-span-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="date_debut" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Du</label>
                                        <input type="date" id="date_debut" name="date_debut" value="{{ $dateDebut }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="date_fin" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Au</label>
                                        <input type="date" id="date_fin" name="date_fin" value="{{ $dateFin }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <input type="hidden" name="periode" value="personnalise" id="periodeInput">
                            </div>

                            <!-- Type de transaction -->
                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Type</label>
                                <select id="type" name="type"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="tous" {{ $type === 'tous' ? 'selected' : '' }}>Tous les types</option>
                                    <option value="inscription" {{ $type === 'inscription' ? 'selected' : '' }}>Inscriptions</option>
                                    <option value="reabonnement" {{ $type === 'reabonnement' ? 'selected' : '' }}>Réabonnements</option>
                                </select>
                            </div>

                            <!-- Mode de paiement -->
                            <div>
                                <label for="mode_paiement" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mode paiement</label>
                                <select id="mode_paiement" name="mode_paiement"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="tous" {{ $modePaiement === 'tous' ? 'selected' : '' }}>Tous les modes</option>
                                    <option value="especes" {{ $modePaiement === 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                                    <option value="mobile_money" {{ $modePaiement === 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                                    <option value="carte_bancaire" {{ $modePaiement === 'carte_bancaire' ? 'selected' : '' }}>💳 Carte bancaire</option>
                                    <option value="virement" {{ $modePaiement === 'virement' ? 'selected' : '' }}>🏦 Virement</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div>
                                <label for="statut" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                                <select id="statut" name="statut"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="tous" {{ $statut === 'tous' ? 'selected' : '' }}>Tous les statuts</option>
                                    <option value="paye" {{ $statut === 'paye' ? 'selected' : '' }}>✅ Payé</option>
                                    <option value="en_attente" {{ $statut === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                                </select>
                            </div>

                            <!-- Encaissé par -->
                            <div>
                                <label for="encaisse_par" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Encaissé par</label>
                                <select id="encaisse_par" name="encaisse_par"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Tous les agents</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $encaissePar == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Recherche -->
                            <div class="lg:col-span-2">
                                <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recherche client</label>
                                <div class="relative">
                                    <input type="text" id="search" name="search" value="{{ $search }}"
                                           placeholder="Nom, prénom ou n° dossier..."
                                           class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex gap-2">
                                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                    Filtrer
                                </button>
                                <a href="{{ route('transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2 rounded-lg font-medium transition duration-200">
                                    Réinitialiser
                                </a>
                            </div>
                            <a href="{{ route('transactions.export', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Encaissé</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total'], 0, ',', ' ') }} XAF</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $stats['count'] }} transaction(s)</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Inscriptions</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['inscriptions'], 0, ',', ' ') }} XAF</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $stats['inscriptions_count'] }} inscription(s)</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Réabonnements</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['reabonnements'], 0, ',', ' ') }} XAF</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $stats['reabonnements_count'] }} réabonnement(s)</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Période</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                @if($dateDebut === $dateFin)
                                    {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($dateDebut)->format('d/m') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des transactions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-white font-semibold text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Liste des Transactions
                    </h3>
                    <!-- Tri -->
                    <div class="flex items-center space-x-2">
                        <span class="text-white text-sm">Trier par:</span>
                        <select id="sortSelect" onchange="applySort()" class="bg-gray-600 text-white text-sm rounded-lg px-3 py-1 border-0 focus:ring-2 focus:ring-white">
                            <option value="date" {{ $sortBy === 'date' ? 'selected' : '' }}>Date</option>
                            <option value="montant" {{ $sortBy === 'montant' ? 'selected' : '' }}>Montant</option>
                            <option value="client" {{ $sortBy === 'client' ? 'selected' : '' }}>Client</option>
                            <option value="type" {{ $sortBy === 'type' ? 'selected' : '' }}>Type</option>
                            <option value="mode" {{ $sortBy === 'mode' ? 'selected' : '' }}>Mode</option>
                        </select>
                        <button type="button" onclick="toggleSortOrder()" class="bg-gray-600 hover:bg-gray-500 text-white p-1 rounded-lg">
                            <svg class="w-5 h-5 transition-transform {{ $sortOrder === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Heure</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mode</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Encaissé par</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $transaction['date']?->format('d/m/Y') ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $transaction['date']?->format('H:i') ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($transaction['dossier']?->photo_profil_path)
                                            <img src="/storage/{{ $transaction['dossier']->photo_profil_path }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xs mr-3">
                                                {{ substr($transaction['dossier']?->prenom ?? '', 0, 1) }}{{ substr($transaction['dossier']?->nom ?? '', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $transaction['dossier']?->prenom }} {{ $transaction['dossier']?->nom }}
                                            </div>
                                            <div class="text-xs text-blue-600 dark:text-blue-400 font-mono">
                                                {{ $transaction['dossier']?->numero_unique }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $transaction['type'] === 'inscription' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                        {{ $transaction['type_label'] }}
                                        @if(isset($transaction['nombre_mois']) && $transaction['nombre_mois'] > 1)
                                            <span class="ml-1">({{ $transaction['nombre_mois'] }} mois)</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($transaction['montant'], 0, ',', ' ') }} XAF
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        @switch($transaction['mode_paiement'])
                                            @case('especes') 💵 Espèces @break
                                            @case('mobile_money') 📱 Mobile Money @break
                                            @case('carte_bancaire') 💳 Carte @break
                                            @case('virement') 🏦 Virement @break
                                            @case('cash') 💵 Cash @break
                                            @default {{ $transaction['mode_paiement'] ?? 'N/A' }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $transaction['statut'] === 'paye' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                        {{ $transaction['statut'] === 'paye' ? '✅ Payé' : '⏳ En attente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $transaction['encaisse_par']?->name ?? 'N/A' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune transaction</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aucune transaction trouvée pour cette période.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleCustomDates() {
            const singleDate = document.getElementById('singleDateContainer');
            const customDates = document.getElementById('customDatesContainer');
            const periodeInput = document.getElementById('periodeInput');

            singleDate.classList.add('hidden');
            customDates.classList.remove('hidden');

            if (!periodeInput) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'periode';
                input.value = 'personnalise';
                input.id = 'periodeInput';
                document.getElementById('filterForm').appendChild(input);
            }
        }

        function applySort() {
            const sortBy = document.getElementById('sortSelect').value;
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            window.location.href = url.toString();
        }

        function toggleSortOrder() {
            const url = new URL(window.location.href);
            const currentOrder = url.searchParams.get('sort_order') || 'desc';
            url.searchParams.set('sort_order', currentOrder === 'desc' ? 'asc' : 'desc');
            window.location.href = url.toString();
        }
    </script>
    @endpush
</x-app-layout>
