<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails du taux de réduction') }}
            </h2>
            <div class="mt-2 flex justify-center space-x-4">
                <a href="{{ route('taux-reductions.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
                <a href="{{ route('taux-reductions.edit', $tauxReduction) }}"
                   class="bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 px-4 py-2 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800 transition duration-200 text-sm font-medium">
                    Modifier
                </a>
                <form action="{{ route('taux-reductions.destroy', $tauxReduction) }}" method="POST" class="inline"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce taux de réduction ?')">
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
                    <h1 class="text-3xl font-bold">{{ $tauxReduction->libelle }}</h1>
                    <p class="text-blue-100 text-lg mt-2">Taux de réduction - {{ $types[$tauxReduction->type] }}</p>
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
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $tauxReduction->libelle }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Taux :</span>
                                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($tauxReduction->taux, 2) }} %</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Type :</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                            @if($tauxReduction->type === 'inscription') bg-blue-100 text-blue-800
                                            @elseif($tauxReduction->type === 'reabonnement') bg-green-100 text-green-800
                                            @elseif($tauxReduction->type === 'contribution') bg-purple-100 text-purple-800
                                            @else bg-orange-100 text-orange-800 @endif">
                                            {{ $types[$tauxReduction->type] }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                            @if($tauxReduction->actif) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $tauxReduction->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Date de création :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $tauxReduction->created_at->format('d/m/Y à H:i') }}</span>
                                    </div>

                                    @if($tauxReduction->updated_at != $tauxReduction->created_at)
                                        <div class="flex justify-between items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                            <span class="font-medium text-gray-600 dark:text-gray-400">Dernière modification :</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $tauxReduction->updated_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Détails -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Détails</h3>
                                @if($tauxReduction->detail)
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $tauxReduction->detail }}</p>
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
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">#{{$tauxReduction->id}}</div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400">ID unique</div>
                                    </div>
                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center border border-green-200 dark:border-green-800">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($tauxReduction->taux, 1) }}%</div>
                                        <div class="text-sm text-green-600 dark:text-green-400">Taux appliqué</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
