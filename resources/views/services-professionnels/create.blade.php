<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Ajouter un Service — {{ $dossierProfessionnel->raison_sociale ?? $dossierProfessionnel->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">Nouveau Service</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('services-pro.store', $dossierProfessionnel) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du Service <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required
                                   placeholder="Ex: Consultation générale, Échographie..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                            @error('nom')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-red-500">*</span></label>
                                <select name="type" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="consultation" @selected(old('type') === 'consultation')>Consultation</option>
                                    <option value="examen" @selected(old('type') === 'examen')>Examen</option>
                                    <option value="hospitalisation" @selected(old('type') === 'hospitalisation')>Hospitalisation</option>
                                    <option value="chirurgie" @selected(old('type') === 'chirurgie')>Chirurgie</option>
                                    <option value="urgence" @selected(old('type') === 'urgence')>Urgence</option>
                                    <option value="autre" @selected(old('type') === 'autre')>Autre</option>
                                </select>
                                @error('type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (XAF) <span class="text-red-500">*</span></label>
                                <input type="number" name="prix" value="{{ old('prix') }}" required min="0" step="100"
                                       placeholder="5000"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                                @error('prix')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('dossier-professionnels.show', $dossierProfessionnel) }}"
                               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2 rounded-lg text-sm transition">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg text-sm transition">
                                Ajouter le Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
