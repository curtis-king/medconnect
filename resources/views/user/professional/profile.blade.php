<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mon dossier professionnel</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivi de votre demande et de sa validation administrative</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($dossierProfessionnel->statut === 'en_attente')
                <div class="p-4 rounded-xl bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700 text-sm">
                    Votre profil professionnel est en attente de validation administrative. La reponse d activation vous sera envoyee par email.
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        @if($dossierProfessionnel->image_identite_path)
                            <img src="{{ Storage::url($dossierProfessionnel->image_identite_path) }}" alt="Visuel professionnel" class="mb-2 w-16 h-16 rounded-lg object-cover border border-white/30">
                        @endif
                        <h3 class="text-white font-semibold text-lg">{{ $dossierProfessionnel->raison_sociale ?? ($dossierProfessionnel->user->name ?? 'Dossier professionnel') }}</h3>
                        <div class="mt-1">
                            @if($dossierProfessionnel->specialite)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30">
                                    Spécialité: {{ $dossierProfessionnel->specialite }}
                                </span>
                            @else
                                <p class="text-emerald-100 text-sm">Spécialité: —</p>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30 w-fit">
                        Statut: {{ $dossierProfessionnel->statut_label }}
                    </span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-2">
                        <p><span class="text-gray-500 dark:text-gray-400">Type de structure:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->type_structure_label }}</span></p>
                        <p><span class="text-gray-500 dark:text-gray-400">NIU:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->NIU ?? '—' }}</span></p>
                        <p><span class="text-gray-500 dark:text-gray-400">Forme juridique:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->forme_juridique ?? '—' }}</span></p>
                        <p><span class="text-gray-500 dark:text-gray-400">Paiement:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $dossierProfessionnel->statut_paiement_inscription)) }}</span>
                        </p>
                    </div>

                    <div class="space-y-3">
                        @if($dossierProfessionnel->attestation_professionnelle_path)
                            <a href="{{ Storage::url($dossierProfessionnel->attestation_professionnelle_path) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline">
                                Voir l'attestation professionnelle
                            </a>
                        @endif
                        @if($dossierProfessionnel->document_prise_de_fonction_path)
                            <a href="{{ Storage::url($dossierProfessionnel->document_prise_de_fonction_path) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline">
                                Voir le document de prise de fonction
                            </a>
                        @endif

                        @if($dossierProfessionnel->statut !== 'valide' && $dossierProfessionnel->statut_paiement_inscription !== 'paye')
                            <a href="{{ route('user.professional.payment', $dossierProfessionnel) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium">
                                Finaliser mon paiement
                            </a>
                        @endif

                        @if($dossierProfessionnel->statut === 'valide')
                            <a href="{{ route('professional.workspace.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium">
                                Accéder à mon espace de travail
                            </a>
                        @else
                            <p class="text-xs text-amber-600 dark:text-amber-400">Votre espace de travail sera accessible après validation de votre profil par l'administration.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-start">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>
