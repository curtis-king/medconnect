<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-center items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Frais d\'Inscription') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8" x-data="{ flashVisible: true }">
        <div class="max-w-7xl mx-auto">
            <!-- Success Message -->
            @if(session('success'))
                <div
                    x-show="flashVisible"
                    x-transition.opacity.duration.400ms
                    x-init="setTimeout(() => flashVisible = false, 4000)"
                    class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center shadow-md"
                >
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste des frais</h3>
                        <a href="{{ route('frais-inscriptions.create') }}" class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                            Nouveau frais
                        </a>
                    </div>
                    @if($fraisInscriptions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Libellé
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Montant
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Détail
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date création
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($fraisInscriptions as $frais)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $frais->libelle }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                    {{ number_format($frais->montant, 2) }} XAF
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate mx-auto">
                                                    {{ $frais->detail ?? 'Aucun détail' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $frais->created_at->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center space-x-3">
                                                    <a href="{{ route('frais-inscriptions.show', $frais) }}"
                                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-blue-600">
                                                        Voir
                                                    </a>
                                                    <a href="{{ route('frais-inscriptions.edit', $frais) }}"
                                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-yellow-500">
                                                        Modifier
                                                    </a>
                                                    <form action="{{ route('frais-inscriptions.destroy', $frais) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce frais ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 text-xs font-medium shadow-md border border-red-600">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-green-50 dark:from-gray-700 dark:to-gray-600 rounded-lg border border-blue-200 dark:border-gray-600">
                            <div class="text-center">
                                <span class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total des frais :</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400 block mt-2">
                                    {{ number_format($fraisInscriptions->sum('montant'), 2) }} XAF
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Aucun frais d'inscription</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Commencez par créer votre premier frais d'inscription.</p>
                            <a href="{{ route('frais-inscriptions.create') }}"
                               class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                Créer le premier frais
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
