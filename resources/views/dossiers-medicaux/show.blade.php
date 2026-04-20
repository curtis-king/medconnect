<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dossier Médical - ') . $dossier->numero_unique }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('dossier-medicals.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Détails du dossier') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Profil patient, identité et statut d\'inscription.') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('dossier-medicals.edit', $dossier->id) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200 text-sm font-medium shadow-md border border-yellow-500">
                                {{ __('Modifier') }}
                            </a>
                            <form action="{{ route('dossier-medicals.destroy', $dossier->id) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier médical ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm font-medium shadow-md border border-red-600">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <div class="p-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-blue-50 to-green-50 dark:from-gray-700 dark:to-gray-600">
                                <div class="flex items-center space-x-4">
                                    @if($dossier->photo_profil_path)
                                        <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                                             alt="{{ $dossier->nom_complet }}"
                                             class="w-10 h-10 min-w-[40px] max-w-[40px] min-h-[40px] max-h-[40px] rounded-full object-cover shadow-md border-2 border-white dark:border-gray-600 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 min-w-[40px] max-w-[40px] rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($dossier->prenom, 0, 1).substr($dossier->nom, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $dossier->prenom }} {{ $dossier->nom }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                            {{ $dossier->numero_unique }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">{{ __('Source') }}</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $dossier->source_creation === 'guichet' ? 'Guichet' : 'En ligne' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">{{ __('Statut') }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dossier->actif ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $dossier->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-300">{{ __('Paiement') }}</span>
                                        @php
                                            $badgeClasses = match ($dossier->statut_paiement_inscription) {
                                                'paye' => 'bg-green-100 text-green-800',
                                                'exonere' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-red-100 text-red-800',
                                            };
                                            $label = match ($dossier->statut_paiement_inscription) {
                                                'paye' => 'Payé',
                                                'exonere' => 'Exonéré',
                                                default => 'En attente',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Informations personnelles') }}</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Date de naissance') }}</span>
                                            <span class="text-gray-900 dark:text-gray-100">{{ $dossier->date_naissance?->format('d/m/Y') ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Sexe') }}</span>
                                            <span class="text-gray-900 dark:text-gray-100">{{ $dossier->sexe ?? '—' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Téléphone') }}</span>
                                            <span class="text-gray-900 dark:text-gray-100">{{ $dossier->telephone ?? '—' }}</span>
                                        </div>
                                        <div class="pt-2">
                                            <div class="text-gray-500 dark:text-gray-400">{{ __('Adresse') }}</div>
                                            <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->adresse ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Inscription') }}</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Frais') }}</span>
                                            <span class="text-gray-900 dark:text-gray-100">
                                                {{ $dossier->frais?->libelle ?? '—' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Montant') }}</span>
                                            <span class="text-gray-900 dark:text-gray-100">
                                                {{ $dossier->frais ? number_format($dossier->frais->prix, 2) . ' XAF' : '—' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('Référence') }}</span>
                                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ $dossier->reference_paiement_inscription ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Informations médicales') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Groupe sanguin') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $dossier->groupe_sanguin ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Allergies') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->allergies ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Maladies chroniques') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->maladies_chroniques ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Traitements en cours') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->traitements_en_cours ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Antécédents familiaux') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->antecedents_familiaux ?? '—' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ __('Antécédents personnels') }}</div>
                                        <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->antecedents_personnels ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($dossier->contact_urgence_nom || $dossier->contact_urgence_telephone)
                                <div class="p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Contact d\'urgence') }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                                        <div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ __('Nom') }}</div>
                                            <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_nom ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ __('Téléphone') }}</div>
                                            <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_telephone ?? '—' }}</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ __('Relation') }}</div>
                                            <div class="mt-1 text-gray-900 dark:text-gray-100">{{ $dossier->contact_urgence_relation ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

