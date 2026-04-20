<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-center items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dossiers Patients En Attente de Validation') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste admin des dossiers en attente</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Action rapide vers l'espace de contrôle client pour traiter la validation.</p>
                    </div>

                    @if($dossiers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Patient</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Dossier</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paiement</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Validation pièces</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($dossiers as $dossier)
                                        @php
                                            $requiresAdultVerification = ! $dossier->est_personne_a_charge || optional($dossier->date_naissance)?->age >= 18;
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $dossier->nom_complet }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dossier->user?->email ?? 'Aucun compte lié' }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-mono text-gray-800 dark:text-gray-100">{{ $dossier->numero_unique }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dossier->source_creation }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dossier->statut_paiement_inscription === 'paye' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $dossier->statut_paiement_inscription === 'paye' ? 'Payé' : 'En attente' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                @if($requiresAdultVerification)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($dossier->documents_validation_statut ?? 'en_attente') === 'valide' ? 'bg-green-100 text-green-800' : (($dossier->documents_validation_statut ?? 'en_attente') === 'rejete' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst((string) ($dossier->documents_validation_statut ?? 'en_attente')) }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Mineur</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dossier->actif ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $dossier->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center gap-3">
                                                    <a href="{{ route('controle-client.index', ['dossier' => $dossier->id, 'query' => $dossier->numero_unique]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-blue-600">
                                                        Contrôler
                                                    </a>
                                                    <a href="{{ route('dossier-medicals.show', $dossier) }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-slate-600">
                                                        Voir profil
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $dossiers->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            Aucun dossier patient en attente pour le moment.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
