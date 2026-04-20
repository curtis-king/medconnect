<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dossier médical patient
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vue professionnelle — données médicales autorisées</p>
            </div>
            <a href="javascript:history.back()"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Identity card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-500 px-6 py-5 text-white">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-14 h-14 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center text-2xl font-bold">
                            {{ strtoupper(substr($dossierMedical->prenom ?? $dossierMedical->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold">
                                {{ $dossierMedical->prenom ? $dossierMedical->prenom . ' ' . ($dossierMedical->nom ?? '') : ($dossierMedical->user?->name ?? '—') }}
                            </h3>
                            <p class="text-teal-100 text-sm mt-0.5">N° dossier : {{ $dossierMedical->numero_unique }}</p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/20 border border-white/30 text-xs font-semibold">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Dossier actif
                                </span>
                                @if($dossierMedical->sexe)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/20 border border-white/30 text-xs font-semibold capitalize">
                                        {{ $dossierMedical->sexe }}
                                    </span>
                                @endif
                                @if($dossierMedical->groupe_sanguin)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-400/40 border border-red-300/30 text-xs font-semibold">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12z" clip-rule="evenodd"/></svg>
                                        Groupe {{ $dossierMedical->groupe_sanguin }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date de naissance</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $dossierMedical->date_naissance ? $dossierMedical->date_naissance->format('d/m/Y') : '—' }}
                        </p>
                        @if($dossierMedical->date_naissance)
                            <p class="text-xs text-gray-400">{{ $dossierMedical->date_naissance->age }} ans</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Téléphone</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $dossierMedical->telephone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Adresse</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $dossierMedical->adresse ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Groupe sanguin</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $dossierMedical->groupe_sanguin ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Medical data grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Allergies --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Allergies connues</h4>
                    </div>
                    @if($dossierMedical->allergies)
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $dossierMedical->allergies }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucune allergie renseignée</p>
                    @endif
                </div>

                {{-- Maladies chroniques --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Maladies chroniques</h4>
                    </div>
                    @if($dossierMedical->maladies_chroniques)
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $dossierMedical->maladies_chroniques }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucune maladie chronique renseignée</p>
                    @endif
                </div>

                {{-- Traitements en cours --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Traitements en cours</h4>
                    </div>
                    @if($dossierMedical->traitements_en_cours)
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $dossierMedical->traitements_en_cours }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucun traitement renseigné</p>
                    @endif
                </div>

                {{-- Antécédents --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Antécédents</h4>
                    </div>
                    @if($dossierMedical->antecedents_personnels || $dossierMedical->antecedents_familiaux)
                        @if($dossierMedical->antecedents_personnels)
                            <p class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide mb-1">Personnels</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed mb-3">{{ $dossierMedical->antecedents_personnels }}</p>
                        @endif
                        @if($dossierMedical->antecedents_familiaux)
                            <p class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide mb-1">Familiaux</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $dossierMedical->antecedents_familiaux }}</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 italic">Aucun antécédent renseigné</p>
                    @endif
                </div>
            </div>

            {{-- Contact d'urgence --}}
            @if($dossierMedical->contact_urgence_nom)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-orange-200 dark:border-orange-800 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300">Contact d'urgence</h4>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nom</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $dossierMedical->contact_urgence_nom }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Téléphone</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $dossierMedical->contact_urgence_telephone ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Relation</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $dossierMedical->contact_urgence_relation ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Disclaimer --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/20 flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Cette vue est réservée à votre usage professionnel et limitée aux données médicales autorisées. Elle est accessible uniquement parce que vous avez réalisé au moins une consultation avec ce patient. Les données affichées ici sont strictement confidentielles.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
