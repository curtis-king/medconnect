<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-center items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dossiers Médicaux') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                {{ __('Liste des dossiers médicaux') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Gérez les profils médicaux des patients et suivez leur statut de paiement.') }}
                            </p>
                        </div>
                        <a href="{{ route('dossier-medicals.create') }}"
                           class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                            {{ __('Nouveau dossier') }}
                        </a>
                    </div>

                    @if($dossiers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-left">
                                        {{ __('Patient') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-left">
                                        {{ __('Numéro unique') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                                        {{ __('Source') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                                        {{ __('Statut dossier') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                                        {{ __('Paiement') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                                        {{ __('Créé le') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($dossiers as $dossier)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                @if($dossier->photo_profil_path)
                                                    <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                                                         alt="{{ $dossier->nom_complet }}"
                                                         style="width: 28px; height: 28px; min-width: 28px; max-width: 28px;"
                                                         class="rounded-full object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">
                                                @else
                                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($dossier->prenom, 0, 1).substr($dossier->nom, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $dossier->prenom }} {{ $dossier->nom }}
                                                    </div>
                                                    @if($dossier->user)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ __('Compte lié :') }} {{ $dossier->user->email }}
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                                            {{ __('Aucun compte lié') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-mono text-gray-800 dark:text-gray-100">
                                                {{ $dossier->numero_unique }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ __('À communiquer au patient') }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $dossier->source_creation === 'guichet'
                                                    ? 'bg-purple-100 text-purple-800'
                                                    : 'bg-blue-100 text-blue-800' }}">
                                                {{ $dossier->source_creation === 'guichet' ? 'Guichet' : 'En ligne' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $dossier->actif ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                                {{ $dossier->actif ? 'Actif' : 'Inactif' }}
                                            </span>
                                            @if($dossier->partage_actif)
                                                <div class="mt-1 text-xs text-blue-600 dark:text-blue-400">
                                                    {{ __('Partage activé') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
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
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dossier->created_at?->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-3">
                                                <a href="{{ route('dossier-medicals.show', $dossier->id) }}"
                                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-blue-600">
                                                    {{ __('Voir') }}
                                                </a>
                                                <a href="{{ route('dossier-medicals.edit', $dossier->id) }}"
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-yellow-500">
                                                    {{ __('Modifier') }}
                                                </a>
                                                <form action="{{ route('dossier-medicals.destroy', $dossier->id) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier médical ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-red-600">
                                                        {{ __('Supprimer') }}
                                                    </button>
                                                </form>
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
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Aucun dossier médical') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">
                                {{ __('Commencez par créer un premier dossier pour un patient.') }}
                            </p>
                            <a href="{{ route('dossier-medicals.create') }}"
                               class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                {{ __('Créer un premier dossier') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

