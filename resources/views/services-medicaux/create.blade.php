<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl bg-gradient-to-r from-emerald-600 to-cyan-600 bg-clip-text text-transparent">
                Nouveau Service Médical
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Créez un nouveau service pour votre catalogue médical</p>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <form method="POST" action="{{ route('services-medicaux.store') }}" class="space-y-8">
            @csrf

            <!-- Information de base -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white ml-3">Information de base</h3>
                </div>

                <div class="space-y-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Nom du service <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: Consultation généraliste" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-all duration-300 @error('nom') border-red-500 @enderror">
                        @error('nom')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Description</label>
                        <textarea name="description" rows="4" placeholder="Entrez une description détaillée du service..." class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-all duration-300 resize-none @error('description') border-red-500 @enderror"></textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Détails du tarif -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white ml-3">Détails du tarif</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prix -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Prix (Fcfa) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="prix" value="{{ old('prix') }}" required placeholder="0" min="0" step="1" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-all duration-300 @error('prix') border-red-500 @enderror">
                            <span class="absolute right-4 top-3 text-gray-500 dark:text-gray-400 font-semibold">Fcfa</span>
                        </div>
                        @error('prix')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-all duration-300 @error('type') border-red-500 @enderror">
                            <option value="" selected disabled>Sélectionnez un type</option>
                            @foreach(App\Models\ServiceMedical::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Paramètres -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white ml-3">Paramètres</h3>
                </div>

                <div class="space-y-4">
                    <label class="flex items-center p-4 rounded-lg border-2 border-transparent border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-900/30 cursor-pointer hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                        <input type="checkbox" name="actif" value="1" checked class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Service actif</span>
                            <span class="block text-xs text-gray-600 dark:text-gray-400 mt-1">Ce service sera visible et disponible à la réservation</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('services-medicaux.index') }}" class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-all duration-300">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 font-semibold text-white bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-600 hover:to-cyan-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                    Créer le service
                </button>
            </div>
        </form>
    </div>
</x-app-layout>