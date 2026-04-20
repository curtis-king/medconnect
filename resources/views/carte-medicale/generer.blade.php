<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Génération Carte Médicale PVC') }}
            </h2>
            <div class="mt-2">
                <a href="{{ route('carte-medicale.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 text-sm">
                    ← {{ __('Retour à la recherche') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Contrôles -->
            <div class="mb-8 flex flex-wrap gap-4 items-center justify-between bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aperçu de la carte pour</p>
                        <p class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $dossier->prenom }} {{ $dossier->nom }}</p>
                        <p class="text-emerald-600 dark:text-emerald-400 font-mono text-sm">{{ $dossier->numero_unique }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('carte-medicale.imprimer', $dossier->id) }}" target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-emerald-600 text-white rounded-xl hover:from-blue-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimer la Carte
                    </a>
                </div>
            </div>

            <!-- Cartes recto et verso -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- RECTO -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center flex items-center justify-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 text-white flex items-center justify-center text-sm">1</span>
                        RECTO (Face avant)
                    </h3>
                    <div class="flex justify-center perspective-1000">
                        <div class="card-container relative" style="width: 400px; height: 252px;">
                            <!-- Effet de lumière/reflet -->
                            <div class="absolute inset-0 rounded-3xl bg-gradient-to-tr from-transparent via-white/10 to-transparent pointer-events-none z-10"></div>

                            <!-- Carte -->
                            <div class="relative w-full h-full rounded-3xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-500"
                                 style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 25%, #10b981 50%, #059669 75%, #047857 100%);">

                                <!-- Motif géométrique moderne -->
                                <div class="absolute inset-0 opacity-20">
                                    <svg class="w-full h-full" viewBox="0 0 400 252">
                                        <defs>
                                            <pattern id="modernPattern" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
                                                <circle cx="30" cy="30" r="20" fill="none" stroke="white" stroke-width="0.5" opacity="0.3"/>
                                                <circle cx="30" cy="30" r="10" fill="none" stroke="white" stroke-width="0.3" opacity="0.2"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100%" height="100%" fill="url(#modernPattern)"/>
                                        <!-- Formes abstraites -->
                                        <ellipse cx="350" cy="50" rx="100" ry="80" fill="white" opacity="0.05"/>
                                        <ellipse cx="-20" cy="200" rx="120" ry="100" fill="white" opacity="0.05"/>
                                    </svg>
                                </div>

                                <!-- Contenu de la carte -->
                                <div class="relative h-full p-6 flex flex-col z-20">
                                    <!-- En-tête avec logo et groupe sanguin -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg border border-white/30">
                                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white font-extrabold text-lg tracking-wider drop-shadow-lg">MEDCONNECT</p>
                                                <p class="text-white/80 text-xs font-medium tracking-wide">Carte Santé Officielle</p>
                                            </div>
                                        </div>
                                        @if($dossier->groupe_sanguin)
                                        <div class="bg-white text-red-600 px-4 py-2 rounded-xl text-sm font-black shadow-lg">
                                            {{ $dossier->groupe_sanguin }}
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Section principale avec photo et infos -->
                                    <div class="flex-1 flex items-center space-x-4">
                                        <!-- Photo de profil avec bordure stylée -->
                                        <div class="relative">
                                            <div class="absolute -inset-1 bg-gradient-to-r from-white/50 to-white/20 rounded-xl blur"></div>
                                            @if($dossier->photo_profil_path)
                                                <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                                                     alt="Photo"
                                                     class="relative w-16 h-20 rounded-xl object-cover shadow-xl border-2 border-white/50">
                                            @else
                                                <div class="relative w-16 h-20 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-xl font-bold shadow-xl border-2 border-white/50">
                                                    {{ strtoupper(substr($dossier->prenom, 0, 1) . substr($dossier->nom, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Informations personnelles -->
                                        <div class="flex-1 space-y-2">
                                            <div>
                                                <p class="text-white/60 text-xs font-medium uppercase tracking-wider">Nom & Prénom</p>
                                                <p class="text-white font-black text-xl tracking-wide drop-shadow-lg">
                                                    {{ strtoupper($dossier->nom) }}
                                                </p>
                                                <p class="text-white/90 font-semibold text-lg">
                                                    {{ $dossier->prenom }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-4 pt-1">
                                                <div class="flex items-center space-x-2 bg-white/10 rounded-lg px-3 py-1.5 backdrop-blur-sm">
                                                    <span class="text-white/90 text-sm font-medium">{{ $dossier->sexe === 'Masculin' ? 'Homme' : 'Femme' }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2 bg-white/10 rounded-lg px-3 py-1.5 backdrop-blur-sm">
                                                    <span class="text-white/90 text-sm font-medium">{{ $dossier->date_naissance?->format('d/m/Y') ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pied de carte avec numéro et statut -->
                                    <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-white/10 rounded-lg px-3 py-1.5 backdrop-blur-sm">
                                                <p class="text-white/60 text-xs uppercase tracking-wider">N° Carte</p>
                                                <p class="text-white font-mono font-bold text-sm tracking-widest">{{ $dossier->numero_unique }}</p>
                                            </div>
                                        </div>
                                        @if($dossier->activeSubscription)
                                            <div class="flex items-center space-x-2 bg-emerald-400/30 text-white px-4 py-2 rounded-xl backdrop-blur-sm">
                                                <span class="w-3 h-3 bg-emerald-300 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></span>
                                                <span class="font-bold text-sm tracking-wide">ACTIVE</span>
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-2 bg-amber-400/30 text-white px-4 py-2 rounded-xl backdrop-blur-sm">
                                                <span class="w-3 h-3 bg-amber-300 rounded-full"></span>
                                                <span class="font-bold text-sm tracking-wide">EXPIRÉE</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VERSO -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center flex items-center justify-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 text-white flex items-center justify-center text-sm">2</span>
                        VERSO (Face arrière)
                    </h3>
                    <div class="flex justify-center">
                        <div class="card-container relative" style="width: 400px; height: 252px;">
                            <div class="relative w-full h-full rounded-3xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-500"
                                 style="background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);">

                                <!-- Bande de sécurité stylée -->
                                <div class="absolute top-5 left-0 right-0 h-12" style="background: linear-gradient(90deg, #1e293b 0%, #334155 50%, #1e293b 100%);"></div>

                                <!-- Ligne décorative -->
                                <div class="absolute top-[72px] left-4 right-4 h-0.5 bg-gradient-to-r from-transparent via-blue-400/50 to-transparent"></div>

                                <!-- Contenu -->
                                <div class="relative h-full p-5 pt-20">
                                    <div class="flex gap-6">
                                        <!-- Contact urgence -->
                                        <div class="flex-1 space-y-3">
                                            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4">
                                                <p class="text-emerald-800 text-xs font-bold mb-2">Contact d'urgence</p>
                                                @if($dossier->contact_urgence_nom)
                                                    <p class="text-emerald-700 text-sm font-medium">{{ $dossier->contact_urgence_nom }}</p>
                                                    <p class="text-emerald-600 text-sm font-mono font-bold">{{ $dossier->contact_urgence_telephone ?? 'N/A' }}</p>
                                                    @if($dossier->contact_urgence_relation)
                                                    <p class="text-emerald-500 text-xs mt-1">{{ $dossier->contact_urgence_relation }}</p>
                                                    @endif
                                                @else
                                                    <p class="text-gray-400 text-xs italic">Non renseigné</p>
                                                @endif
                                            </div>

                                            @if($dossier->groupe_sanguin)
                                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                                                <p class="text-blue-800 text-xs font-bold">Groupe Sanguin</p>
                                                <p class="text-blue-700 text-lg font-black">{{ $dossier->groupe_sanguin }}</p>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- QR Code médical -->
                                        <div class="flex flex-col items-center justify-center">
                                            <div id="qrcode-preview" class="w-24 h-24 bg-white rounded-xl shadow-lg flex items-center justify-center border-2 border-gray-200 p-2">
                                                <!-- QR Code généré par JS -->
                                            </div>
                                            <p class="text-gray-500 text-xs mt-2 text-center">Scanner pour<br>infos médicales</p>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="absolute bottom-4 left-5 right-5 flex items-center justify-between">
                                        <div class="text-xs">
                                            @if($dossier->activeSubscription)
                                                <span class="text-gray-500">Valide jusqu'au </span>
                                                <span class="font-bold text-emerald-600">{{ $dossier->activeSubscription->date_fin->format('d/m/Y') }}</span>
                                            @else
                                                <span class="text-amber-600 font-semibold">Abonnement expiré</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 font-mono">
                                            medconnect.cm
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions d'impression -->
            <div class="mt-12 bg-gradient-to-r from-blue-50 to-emerald-50 dark:from-blue-900/30 dark:to-emerald-900/30 rounded-3xl p-8 border border-blue-200/50 dark:border-blue-700/50 shadow-lg">
                <h4 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-emerald-600 bg-clip-text text-transparent mb-6 flex items-center">
                    <span class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 text-white flex items-center justify-center mr-3">
                        📋
                    </span>
                    Instructions d'impression PVC
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center text-lg font-bold mb-3">1</div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Format de carte</p>
                        <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">85.60 × 53.98 mm (CR80)</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center text-lg font-bold mb-3">2</div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Résolution</p>
                        <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">300 DPI minimum</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center text-lg font-bold mb-3">3</div>
                        <p class="font-bold text-gray-800 dark:text-gray-200">Support</p>
                        <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">Carte PVC blanche 0.76mm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .perspective-1000 {
            perspective: 1000px;
        }
        .card-container:hover {
            transform: rotateY(5deg) rotateX(2deg);
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrUrl = @js(route('carte-medicale.scan', ['code' => $dossier->code_partage ?? $dossier->numero_unique]));
            const qrContainer = document.getElementById('qrcode-preview');

            if (!qrContainer) {
                return;
            }

            if (!window.QRCode || typeof window.QRCode.toCanvas !== 'function') {
                qrContainer.innerHTML = '<span class="text-[10px] text-amber-600 text-center leading-tight">QR indisponible</span>';
                return;
            }

            const canvas = document.createElement('canvas');

            window.QRCode.toCanvas(canvas, qrUrl, {
                width: 80,
                margin: 1,
                color: {
                    dark: '#1e293b',
                    light: '#ffffff'
                }
            }, function(error) {
                if (error) {
                    console.error('QR preview generation failed:', error);
                    qrContainer.innerHTML = '<span class="text-[10px] text-amber-600 text-center leading-tight">QR indisponible</span>';
                    return;
                }

                qrContainer.innerHTML = '';
                qrContainer.appendChild(canvas);
            });
        });
    </script>
    @endpush
</x-app-layout>
