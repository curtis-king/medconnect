<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Demande de Carte Médicale') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('carte-medicale.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm">
                    ← {{ __('Retour à la recherche') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <!-- En-tête avec photo et infos principales -->
                    <div class="flex flex-col md:flex-row md:items-start gap-8 mb-8">
                        <!-- Photo et numéro -->
                        <div class="flex-shrink-0 text-center">
                            @if($dossier->photo_profil_path)
                                <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                                     alt="{{ $dossier->prenom }} {{ $dossier->nom }}"
                                     class="w-20 h-20 rounded-xl object-cover shadow-md border-2 border-indigo-200 dark:border-indigo-700 mx-auto">
                            @else
                                <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-md mx-auto">
                                    {{ strtoupper(substr($dossier->prenom, 0, 1) . substr($dossier->nom, 0, 1)) }}
                                </div>
                            @endif
                            <p class="mt-3 font-mono text-indigo-600 dark:text-indigo-400 text-sm font-semibold">
                                {{ $dossier->numero_unique }}
                            </p>
                        </div>

                        <!-- Informations principales -->
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $dossier->prenom }} {{ $dossier->nom }}
                            </h3>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $dossier->date_naissance?->format('d/m/Y') ?? 'Non renseigné' }}</span>
                                    @if($dossier->date_naissance)
                                        <span class="ml-2 text-xs text-gray-500">({{ $dossier->date_naissance->age }} ans)</span>
                                    @endif
                                </div>

                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>{{ $dossier->telephone ?? 'Non renseigné' }}</span>
                                </div>

                                @if($dossier->groupe_sanguin)
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                        🩸 {{ $dossier->groupe_sanguin }}
                                    </span>
                                </div>
                                @endif

                                <div class="flex items-center">
                                    @if($dossier->actif)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                            ✓ Dossier actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            ✗ Dossier inactif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statut abonnement -->
                    <div class="mb-8 p-4 rounded-xl {{ $dossier->activeSubscription ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800' : 'bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800' }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($dossier->activeSubscription)
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium {{ $dossier->activeSubscription ? 'text-green-800 dark:text-green-300' : 'text-yellow-800 dark:text-yellow-300' }}">
                                    Statut de l'abonnement
                                </h4>
                                <p class="text-sm {{ $dossier->activeSubscription ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                                    @if($dossier->activeSubscription)
                                        Abonnement valide jusqu'au {{ $dossier->activeSubscription->date_fin->format('d/m/Y') }}
                                    @else
                                        Aucun abonnement actif - La carte peut être générée mais sera marquée comme expirée.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations pour la carte -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Informations médicales</h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Groupe sanguin</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->groupe_sanguin ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Allergies</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->allergies ? 'Oui' : 'Non' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Maladies chroniques</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->maladies_chroniques ? 'Oui' : 'Non' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Contact d'urgence</h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Nom</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_nom ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Téléphone</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_telephone ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Relation</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_relation ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('carte-medicale.generer', $dossier->id) }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition duration-200 font-medium shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            Générer la Carte PVC
                        </a>

                        <a href="{{ route('dossier-medicals.show', $dossier->id) }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition duration-200 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Voir le Dossier Complet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
