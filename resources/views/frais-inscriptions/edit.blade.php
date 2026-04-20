<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier le frais d\'inscription') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('frais-inscriptions.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-10">
                    <!-- Current Frais Info -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Frais actuel :</h3>
                        <p class="text-blue-700 dark:text-blue-300">
                            <strong>{{ $fraisInscription->libelle }}</strong> -
                            <span class="font-bold text-green-600 dark:text-green-400">{{ number_format($fraisInscription->montant, 2) }} XAF</span>
                        </p>
                        @if($fraisInscription->detail)
                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">{{ $fraisInscription->detail }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('frais-inscriptions.update', $fraisInscription) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Libellé -->
                        <div>
                            <label for="libelle" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Libellé du frais *
                            </label>
                            <input id="libelle" type="text" name="libelle" value="{{ old('libelle', $fraisInscription->libelle) }}" required
                                class="px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                placeholder="Ex: Frais d'inscription annuel">
                            <x-input-error :messages="$errors->get('libelle')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Montant -->
                        <div>
                            <label for="montant" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Montant (XAF) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">XAF</span>
                                <input id="montant" type="number" name="montant" value="{{ old('montant', $fraisInscription->montant) }}" required step="0.01" min="0"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                    placeholder="0.00">
                            </div>
                            <x-input-error :messages="$errors->get('montant')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Détail -->
                        <div>
                            <label for="detail" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Détails (optionnel)
                            </label>
                            <textarea id="detail" name="detail" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200 resize-none"
                                placeholder="Description détaillée du frais...">{{ old('detail', $fraisInscription->detail) }}</textarea>
                            <x-input-error :messages="$errors->get('detail')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200 dark:border-gray-600">
                            <form action="{{ route('frais-inscriptions.destroy', $fraisInscription) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce frais ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition duration-200 text-sm font-medium shadow-md border border-red-600">
                                    Supprimer
                                </button>
                            </form>

                            <div class="flex space-x-6">
                                <a href="{{ route('frais-inscriptions.index') }}"
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
