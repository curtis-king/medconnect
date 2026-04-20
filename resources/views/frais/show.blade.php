<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails du frais') }}
            </h2>
            <div class="mt-2 flex justify-center space-x-4">
                <a href="{{ route('frais.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
                <a href="{{ route('frais.edit', $frai) }}"
                   class="bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 px-4 py-2 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800 transition duration-200 text-sm font-medium">
                    Modifier
                </a>
                <form action="{{ route('frais.destroy', $frai) }}" method="POST" class="inline"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce frais ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 px-4 py-2 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition duration-200 text-sm font-medium">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <!-- Header with gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 p-8 text-white text-center">
                    <h1 class="text-3xl font-bold">{{ $frai->libelle }}</h1>
                    <p class="text-blue-100 text-lg mt-2">Frais - {{ \App\Models\Frais::TYPES[$frai->type] }}</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informations principales -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Informations générales</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Libellé :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $frai->libelle }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Prix :</span>
                                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($frai->prix, 2) }} XAF</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Type :</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                            @if($frai->type === 'inscription') bg-blue-100 text-blue-800
                                            @elseif($frai->type === 'reabonnement') bg-green-100 text-green-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                            {{ \App\Models\Frais::TYPES[$frai->type] }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Date de création :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $frai->created_at->format('d/m/Y à H:i') }}</span>
                                    </div>

                                    @if($frai->updated_at != $frai->created_at)
                                        <div class="flex justify-between items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                            <span class="font-medium text-gray-600 dark:text-gray-400">Dernière modification :</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $frai->updated_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Détails -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Détails</h3>
                                @if($frai->detail)
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $frai->detail }}</p>
                                    </div>
                                @else
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">Aucun détail supplémentaire fourni</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Statistiques -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Statistiques</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center border border-blue-200 dark:border-blue-800">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">#{{ $frai->id }}</div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400">ID unique</div>
                                    </div>
                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center border border-green-200 dark:border-green-800">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($frai->prix, 0) }}</div>
                                        <div class="text-sm text-green-600 dark:text-green-400">XAF</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-wrap gap-6 justify-center">
                            <a href="{{ route('frais.edit', $frai) }}"
                               class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-800 shadow-lg border-2 border-yellow-500 hover:border-yellow-600">
                                Modifier ce frais
                            </a>
                            <a href="{{ route('frais.index') }}"
                               class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-3 px-8 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200 border border-gray-300 dark:border-gray-500">
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
