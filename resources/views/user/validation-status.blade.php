<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Validation de mes profils</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivi des profils patient et professionnel en attente d activation</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 p-4">
                <p class="text-sm text-blue-900 dark:text-blue-100 font-medium">
                    La reponse de l activation est transmise automatiquement par email a votre adresse de compte.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Profils patients</h3>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($medicalDossiers as $dossier)
                        @php
                            $paymentPaid = ($dossier->statut_paiement_inscription ?? 'en_attente') === 'paye';
                            $documentStatus = $dossier->documents_validation_statut ?? 'en_attente';
                            $statusLabel = 'En attente';
                            $statusClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-700';

                            if ($documentStatus === 'rejete') {
                                $statusLabel = 'Rejete';
                                $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700';
                            } elseif ($paymentPaid && $documentStatus === 'valide' && $dossier->actif) {
                                $statusLabel = 'Active';
                                $statusClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700';
                            }
                        @endphp

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $dossier->nom_complet }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $dossier->numero_unique }}</p>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <p>Paiement inscription: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', (string) ($dossier->statut_paiement_inscription ?? 'en_attente'))) }}</span></p>
                                <p>Validation pieces: <span class="font-medium">{{ ucfirst((string) $documentStatus) }}</span></p>
                                <p>Dossier actif: <span class="font-medium">{{ $dossier->actif ? 'Oui' : 'Non' }}</span></p>
                            </div>

                            @if($documentStatus === 'rejete' && $dossier->documents_validation_personnel_note)
                                <div class="mt-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-3 text-xs text-red-800 dark:text-red-300">
                                    Motif: {{ $dossier->documents_validation_personnel_note }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun profil patient trouve.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Profil professionnel</h3>
                </div>

                <div class="p-6">
                    @if($professionalDossier)
                        @php
                            $proStatus = (string) $professionalDossier->statut;
                            $proClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-700';

                            if ($proStatus === 'valide') {
                                $proClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700';
                            } elseif ($proStatus === 'recale') {
                                $proClass = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-700';
                            }
                        @endphp

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $professionalDossier->raison_sociale ?? ($professionalDossier->user->name ?? 'Profil professionnel') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">NIU: {{ $professionalDossier->NIU ?? 'N/A' }}</p>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $proClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $proStatus)) }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <p>Paiement inscription: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', (string) ($professionalDossier->statut_paiement_inscription ?? 'en_attente'))) }}</span></p>
                                <p>Specialite: <span class="font-medium">{{ $professionalDossier->specialite ?: 'N/A' }}</span></p>
                            </div>

                            @if($professionalDossier->statut === 'recale' && $professionalDossier->notes)
                                <div class="mt-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-3 text-xs text-red-800 dark:text-red-300">
                                    Motif: {{ $professionalDossier->notes }}
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun profil professionnel trouve.</p>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</x-app-layout>
