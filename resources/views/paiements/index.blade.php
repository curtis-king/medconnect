<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Paiements') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liste des Paiements</h3>
                        <a href="{{ route('paiements.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Nouveau Paiement
                        </a>
                    </div>

                    <!-- Recherche Instantanée -->
                    <div class="mb-6">
                        <input type="text" id="searchInput" placeholder="Rechercher par N° Dossier..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    @if($dossierId)
                        <div class="mb-4">
                            <a href="{{ route('dossier-medicals.show', $dossierId) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                ← Retour au dossier médical
                            </a>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dossier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($paiements as $paiement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $paiement->dossierMedical->numero_unique }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ ucfirst($paiement->type_paiement) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $paiement->montant_formatted }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $paiement->periode_debut->format('d/m/Y') }} - {{ $paiement->periode_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($paiement->statut === 'paye') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($paiement->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($paiement->statut === 'annule') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @if($paiement->statut === 'en_attente')
                                        <form action="{{ route('paiements.confirm', $paiement->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Confirmer ce paiement ?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-green-100 px-2 py-1 rounded text-xs transition duration-200">
                                                Confirmer
                                            </button>
                                        </form>
                                        @endif
                                        @if($paiement->statut === 'paye')
                                        <a href="{{ route('paiements.pdf', $paiement->id) }}" target="_blank"
                                           class="bg-blue-600 hover:bg-blue-700 text-blue-100 px-2 py-1 rounded text-xs transition duration-200">
                                            Reçu PDF
                                        </a>
                                        @endif
                                        <a href="{{ route('paiements.show', $paiement->id) }}"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Voir
                                        </a>
                                        <a href="{{ route('paiements.edit', $paiement->id) }}"
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                            Modifier
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Aucun paiement trouvé.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $paiements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchValue = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const dossierId = row.querySelector('td:first-child').textContent.toLowerCase();
            row.style.display = dossierId.includes(searchValue) ? '' : 'none';
        });
    });
    </script>
</x-app-layout>
