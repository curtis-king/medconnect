<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $dossier ? $dossier->prenom . ' ' . $dossier->nom . ' - Carte Médicale' : 'Carte Médicale' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 25%, #10b981 50%, #059669 75%, #047857 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        @if($error)
            <!-- Message d'erreur -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-orange-500 p-6 text-center">
                    <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                        @if(isset($partage_desactive) && $partage_desactive)
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        @else
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @endif
                    </div>
                    <h1 class="text-white text-xl font-bold">
                        @if(isset($partage_desactive) && $partage_desactive)
                            Partage désactivé
                        @else
                            Carte introuvable
                        @endif
                    </h1>
                </div>
                <div class="p-8 text-center">
                    <p class="text-gray-600 mb-6">{{ $error }}</p>
                    <p class="text-sm text-gray-400">
                        @if(isset($partage_desactive) && $partage_desactive)
                            Le titulaire de cette carte doit activer le partage pour que vous puissiez consulter ses informations médicales.
                        @else
                            Vérifiez que le QR code est correct ou contactez le titulaire de la carte.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <!-- Informations médicales -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <!-- En-tête avec photo -->
                <div class="bg-gradient-to-r from-blue-500 via-cyan-500 to-emerald-500 p-6">
                    <div class="flex items-center space-x-4">
                        @if($dossier->photo_profil_path)
                            <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}"
                                 alt="Photo"
                                 class="w-20 h-20 rounded-2xl object-cover border-3 border-white/50 shadow-lg">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-white text-2xl font-bold border-3 border-white/50">
                                {{ strtoupper(substr($dossier->prenom, 0, 1) . substr($dossier->nom, 0, 1)) }}
                            </div>
                        @endif
                        <div class="text-white">
                            <h1 class="text-xl font-bold">{{ $dossier->prenom }} {{ strtoupper($dossier->nom) }}</h1>
                            <p class="text-white/80 text-sm font-mono">{{ $dossier->numero_unique }}</p>
                            <div class="flex items-center space-x-2 mt-2">
                                @if($dossier->activeSubscription)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-400/30 text-white">
                                        <span class="w-2 h-2 bg-emerald-300 rounded-full mr-1.5 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-400/30 text-white">
                                        <span class="w-2 h-2 bg-amber-300 rounded-full mr-1.5"></span>
                                        Expirée
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase font-medium">Date de naissance</p>
                            <p class="text-gray-800 font-semibold">{{ $dossier->date_naissance?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase font-medium">Sexe</p>
                            <p class="text-gray-800 font-semibold">{{ $dossier->sexe ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($dossier->groupe_sanguin)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <p class="text-xs text-red-600 uppercase font-medium">Groupe Sanguin</p>
                        <p class="text-red-700 font-bold text-2xl">{{ $dossier->groupe_sanguin }}</p>
                    </div>
                    @endif

                    <!-- Informations médicales sensibles -->
                    <div class="border-t pt-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Informations Médicales</h3>

                        @if($dossier->allergies)
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 mb-3">
                            <p class="text-red-800 text-xs font-bold uppercase">Allergies</p>
                            <p class="text-red-700 text-sm mt-1">{{ $dossier->allergies }}</p>
                        </div>
                        @endif

                        @if($dossier->maladies_chroniques)
                        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-r-xl p-4 mb-3">
                            <p class="text-orange-800 text-xs font-bold uppercase">Maladies Chroniques</p>
                            <p class="text-orange-700 text-sm mt-1">{{ $dossier->maladies_chroniques }}</p>
                        </div>
                        @endif

                        @if($dossier->traitements_en_cours)
                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-xl p-4 mb-3">
                            <p class="text-blue-800 text-xs font-bold uppercase">Traitements en cours</p>
                            <p class="text-blue-700 text-sm mt-1">{{ $dossier->traitements_en_cours }}</p>
                        </div>
                        @endif

                        @if(!$dossier->allergies && !$dossier->maladies_chroniques && !$dossier->traitements_en_cours)
                        <p class="text-gray-400 text-sm italic text-center py-4">Aucune information médicale renseignée</p>
                        @endif
                    </div>

                    <!-- Contact d'urgence -->
                    @if($dossier->contact_urgence_nom)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <p class="text-xs text-emerald-600 uppercase font-medium">Contact d'urgence</p>
                        <p class="text-emerald-800 font-semibold">{{ $dossier->contact_urgence_nom }}</p>
                        @if($dossier->contact_urgence_telephone)
                        <a href="tel:{{ $dossier->contact_urgence_telephone }}" class="text-emerald-600 font-mono font-bold text-lg hover:underline">
                            {{ $dossier->contact_urgence_telephone }}
                        </a>
                        @endif
                        @if($dossier->contact_urgence_relation)
                        <p class="text-emerald-500 text-sm">{{ $dossier->contact_urgence_relation }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 text-center">
                    <p class="text-xs text-gray-400">
                        Informations partagées via MEDCONNECT
                    </p>
                    <p class="text-xs text-gray-300 mt-1">medconnect.cm</p>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
