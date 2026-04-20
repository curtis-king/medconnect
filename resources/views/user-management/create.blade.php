<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-center items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Créer un Nouvel Utilisateur') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <form method="POST" action="{{ route('user-management.store') }}" class="space-y-8" x-data="userForm()">
                        @csrf

                        {{-- Section: Informations Personnelles --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Informations Personnelles
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Nom complet')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                                 :value="old('name')" required autofocus autocomplete="name"
                                                 placeholder="Ex: Jean Dupont" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Téléphone')" />
                                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                                                 :value="old('phone')" autocomplete="tel"
                                                 placeholder="Ex: +237 6XX XXX XXX" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                                 :value="old('email')" required autocomplete="username"
                                                 placeholder="Ex: jean.dupont@email.com" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Sécurité --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Sécurité
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Mot de passe')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                                 required autocomplete="new-password" />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                                 class="mt-1 block w-full" required autocomplete="new-password" />
                                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Rôle et Statut --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Rôle et Statut
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Role -->
                                <div>
                                    <x-input-label for="role" :value="__('Rôle')" />
                                    <select id="role" name="role" x-model="selectedRole"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Sélectionnez un rôle</option>
                                        @foreach($roles as $key => $label)
                                            <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('role')" />

                                    {{-- Description du rôle --}}
                                    <div x-show="selectedRole" x-transition class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm">
                                        <template x-if="selectedRole === 'livreur'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-blue-600">🛵 Livreur:</span>
                                                Gère les livraisons de médicaments et produits médicaux aux clients.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'financiere'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-emerald-600">💰 Agent Financier:</span>
                                                Gère les paiements, facturations et rapports financiers.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'service_client'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-amber-600">📞 Service Client:</span>
                                                Traite les demandes clients, réclamations et support.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'soignant'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-pink-600">🏥 Soignant de Proximité:</span>
                                                Intervient sur le terrain pour les soins de première nécessité.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'membre'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-indigo-600">👥 Membre Mutualisation:</span>
                                                Membre de la mutuelle avec accès aux services partagés.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'admin'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-red-600">👑 Administrateur:</span>
                                                Accès complet au système et gestion des utilisateurs.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'professional'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-purple-600">🔧 Professionnel:</span>
                                                Personnel médical avec accès aux dossiers patients.
                                            </p>
                                        </template>
                                        <template x-if="selectedRole === 'user'">
                                            <p class="text-gray-600 dark:text-gray-300">
                                                <span class="font-medium text-gray-600">👤 Utilisateur:</span>
                                                Accès basique au système.
                                            </p>
                                        </template>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Statut')" />
                                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>✅ Actif</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>⏸️ Inactif</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Géolocalisation (affiché pour certains rôles) --}}
                        <div x-show="needsGeolocation()" x-transition class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Géolocalisation
                                <span class="ml-2 text-xs text-gray-500 font-normal">(Important pour les livreurs et soignants)</span>
                            </h3>

                            {{-- Bouton localisation automatique --}}
                            <div class="mb-4">
                                <button type="button" @click="getGeolocation()"
                                        :disabled="geoLoading"
                                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 text-white rounded-lg transition-colors">
                                    <svg x-show="!geoLoading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <svg x-show="geoLoading" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="geoLoading ? 'Localisation en cours...' : '📍 Obtenir ma position'"></span>
                                </button>

                                {{-- Message d'erreur géolocalisation --}}
                                <p x-show="geoError" x-text="geoError" class="mt-2 text-sm text-red-600"></p>

                                {{-- Message de succès --}}
                                <p x-show="geoSuccess" class="mt-2 text-sm text-emerald-600">
                                    ✅ Position obtenue avec succès!
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Latitude -->
                                <div>
                                    <x-input-label for="latitude" :value="__('Latitude')" />
                                    <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full bg-gray-50 dark:bg-gray-700"
                                                 x-model="latitude" :value="old('latitude')" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                                </div>

                                <!-- Longitude -->
                                <div>
                                    <x-input-label for="longitude" :value="__('Longitude')" />
                                    <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full bg-gray-50 dark:bg-gray-700"
                                                 x-model="longitude" :value="old('longitude')" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                                </div>

                                <!-- Ville -->
                                <div>
                                    <x-input-label for="city" :value="__('Ville')" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                                 x-model="city" :value="old('city')" placeholder="Ex: Douala" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <!-- Quartier -->
                                <div>
                                    <x-input-label for="quartier" :value="__('Quartier')" />
                                    <x-text-input id="quartier" name="quartier" type="text" class="mt-1 block w-full"
                                                 x-model="quartier" :value="old('quartier')" placeholder="Ex: Akwa, Bonapriso" />
                                    <x-input-error class="mt-2" :messages="$errors->get('quartier')" />
                                </div>

                                <!-- Adresse complète -->
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Adresse complète')" />
                                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                                 x-model="address" :value="old('address')" placeholder="Ex: Rue des Palmiers, Immeuble ABC" />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Profil --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Notes / Profil
                            </h3>

                            <div>
                                <textarea id="profile" name="profile" rows="3"
                                         class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                         placeholder="Notes additionnelles sur l'utilisateur...">{{ old('profile') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('profile')" />
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('user-management.index') }}"
                               class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition duration-200">
                                ← Annuler
                            </a>

                            <div class="flex space-x-3">
                                <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                    Réinitialiser
                                </button>
                                <x-primary-button class="bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700">
                                    {{ __('✅ Créer l\'utilisateur') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function userForm() {
            return {
                selectedRole: '{{ old('role', '') }}',
                latitude: '{{ old('latitude', '') }}',
                longitude: '{{ old('longitude', '') }}',
                address: '{{ old('address', '') }}',
                city: '{{ old('city', '') }}',
                quartier: '{{ old('quartier', '') }}',
                geoLoading: false,
                geoError: '',
                geoSuccess: false,

                needsGeolocation() {
                    return ['livreur', 'soignant', 'membre'].includes(this.selectedRole);
                },

                getGeolocation() {
                    this.geoLoading = true;
                    this.geoError = '';
                    this.geoSuccess = false;

                    if (!navigator.geolocation) {
                        this.geoError = 'La géolocalisation n\'est pas supportée par votre navigateur.';
                        this.geoLoading = false;
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.latitude = position.coords.latitude.toFixed(8);
                            this.longitude = position.coords.longitude.toFixed(8);
                            this.geoLoading = false;
                            this.geoSuccess = true;

                            // Reverse geocoding pour obtenir l'adresse
                            this.reverseGeocode(position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            this.geoLoading = false;
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    this.geoError = 'Vous avez refusé l\'accès à la géolocalisation.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    this.geoError = 'Les informations de localisation ne sont pas disponibles.';
                                    break;
                                case error.TIMEOUT:
                                    this.geoError = 'La demande de localisation a expiré.';
                                    break;
                                default:
                                    this.geoError = 'Une erreur inconnue est survenue.';
                            }
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`);
                        const data = await response.json();

                        if (data && data.address) {
                            this.city = data.address.city || data.address.town || data.address.village || '';
                            this.quartier = data.address.suburb || data.address.neighbourhood || data.address.quarter || '';
                            this.address = data.display_name || '';
                        }
                    } catch (error) {
                        console.error('Erreur reverse geocoding:', error);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
