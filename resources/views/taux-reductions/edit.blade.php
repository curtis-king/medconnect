<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier le taux de réduction') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('taux-reductions.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-10">
                    <!-- Current Taux Info -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Taux actuel :</h3>
                        <p class="text-blue-700 dark:text-blue-300">
                            <strong>{{ $tauxReduction->libelle }}</strong> -
                            <span class="font-bold text-green-600 dark:text-green-400">{{ number_format($tauxReduction->taux, 2) }} %</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                @if($tauxReduction->type === 'inscription') bg-blue-100 text-blue-800
                                @elseif($tauxReduction->type === 'reabonnement') bg-green-100 text-green-800
                                @elseif($tauxReduction->type === 'contribution') bg-purple-100 text-purple-800
                                @else bg-orange-100 text-orange-800 @endif">
                                {{ $types[$tauxReduction->type] }}
                            </span>
                            @if(!$tauxReduction->actif)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 bg-red-100 text-red-800">
                                    Inactif
                                </span>
                            @endif
                        </p>
                        @if($tauxReduction->detail)
                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">{{ $tauxReduction->detail }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('taux-reductions.update', $tauxReduction) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Libellé -->
                        <div>
                            <label for="libelle" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Libellé du taux *
                            </label>
                            <input id="libelle" type="text" name="libelle" value="{{ old('libelle', $tauxReduction->libelle) }}" required
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                placeholder="Ex: Réduction étudiant">
                            <x-input-error :messages="$errors->get('libelle')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Taux -->
                        <div>
                            <label for="taux" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Taux de réduction (%) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">%</span>
                                <input id="taux" type="number" name="taux" value="{{ old('taux', $tauxReduction->taux) }}" required step="0.01" min="0" max="100"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                    placeholder="15.50">
                            </div>
                            <x-input-error :messages="$errors->get('taux')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Type de réduction *
                            </label>
                            <select id="type" name="type" required
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition duration-200">
                                <option value="">Sélectionnez un type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $tauxReduction->type) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Statut Actif -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Statut
                            </label>
                            <div class="flex items-center">
                                <input id="actif" type="checkbox" name="actif" value="1" {{ old('actif', $tauxReduction->actif) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="actif" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Taux actif
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Décochez pour désactiver ce taux de réduction</p>
                        </div>

                        <!-- Détail -->
                        <div>
                            <label for="detail" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Détails (optionnel)
                            </label>
                            <textarea id="detail" name="detail" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200 resize-none"
                                placeholder="Description détaillée du taux de réduction...">{{ old('detail', $tauxReduction->detail) }}</textarea>
                            <x-input-error :messages="$errors->get('detail')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex space-x-6">
                                <a href="{{ route('taux-reductions.index') }}"
                                   class="px-8 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200 font-medium border border-gray-300 dark:border-gray-500">
                                    Annuler
                                </a>
                                <button type="submit"
                                    class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                    Mettre à jour
                                </button>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
