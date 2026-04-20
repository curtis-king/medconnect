<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nouveau frais') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('frais.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-10">
                    <form method="POST" action="{{ route('frais.store') }}" class="space-y-6">
                        @csrf

                        <!-- Libellé -->
                        <div>
                            <label for="libelle" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Libellé du frais *
                            </label>
                            <input id="libelle" type="text" name="libelle" value="{{ old('libelle') }}" required
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                placeholder="Ex: Frais d'inscription annuel">
                            <x-input-error :messages="$errors->get('libelle')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Prix -->
                        <div>
                            <label for="prix" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Prix (Xaf) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">Xaf</span>
                                <input id="prix" type="number" name="prix" value="{{ old('prix') }}" required step="0.01" min="0"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200"
                                    placeholder="0.00">
                            </div>
                            <x-input-error :messages="$errors->get('prix')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Type de frais *
                            </label>
                            <select id="type" name="type" required
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition duration-200">
                                <option value="">Sélectionnez un type</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Détail -->
                        <div>
                            <label for="detail" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Détails (optionnel)
                            </label>
                            <textarea id="detail" name="detail" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 transition duration-200 resize-none"
                                placeholder="Description détaillée du frais...">{{ old('detail') }}</textarea>
                            <x-input-error :messages="$errors->get('detail')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ route('frais.index') }}"
                               class="px-8 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200 font-medium border border-gray-300 dark:border-gray-500">
                                Annuler
                            </a>
                            <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg border-2 border-blue-600 hover:border-blue-700">
                                Enregistrer le frais
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
