<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dossiers Professionnels') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liste des Dossiers Professionnels</h3>
                        <a href="{{ route('dossier-professionnels.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-sm">
                            + Nouveau Dossier
                        </a>
                    </div>

                    <!-- Filtres -->
                    <form method="GET" class="flex flex-wrap gap-3 mb-6">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Rechercher par nom, email..."
                               class="flex-1 min-w-48 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <select name="specialite" class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                            <option value="">Toutes les spécialités</option>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite }}" @selected(request('specialite') === $specialite)>{{ $specialite }}</option>
                            @endforeach
                        </select>
                        <select name="statut" class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                            <option value="valide" @selected(request('statut') === 'valide')>Validé</option>
                            <option value="recale" @selected(request('statut') === 'recale')>Recalé</option>
                        </select>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">Filtrer</button>
                        @if(request()->hasAny(['search', 'statut', 'specialite']))
                            <a href="{{ route('dossier-professionnels.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm transition">Réinitialiser</a>
                        @endif
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Utilisateur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Raison Sociale</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Spécialité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Licence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($dossiers as $dossier)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div>{{ $dossier->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dossier->user->email ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $dossier->raison_sociale ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $dossier->type_structure_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($dossier->specialite)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                                                {{ $dossier->specialite }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                        {{ $dossier->numero_licence ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($dossier->statut === 'valide') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($dossier->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                            {{ $dossier->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('dossier-professionnels.show', $dossier) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 mr-3">Voir</a>
                                                     <a href="{{ route('dossier-professionnels.verification', $dossier) }}"
                                                         class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 mr-3">Vérifier</a>
                                        @if($dossier->isEnAttente())
                                            <form method="POST" action="{{ route('dossier-professionnels.valider', $dossier) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 mr-3"
                                                        onclick="return confirm('Valider ce dossier ?')">Valider</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        Aucun dossier professionnel trouvé.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $dossiers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
