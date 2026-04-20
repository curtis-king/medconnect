<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Vérification du dossier professionnel</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Contrôle administratif des informations et des pièces fournies</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-blue-500 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">{{ $dossierProfessionnel->raison_sociale ?? ($dossierProfessionnel->user->name ?? 'Dossier') }}</h3>
                    <p class="text-indigo-100 text-sm">Utilisateur: {{ $dossierProfessionnel->user->name ?? '—' }} | Email: {{ $dossierProfessionnel->user->email ?? '—' }}</p>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">Informations à vérifier</h4>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-2 text-sm">
                            <p><span class="text-gray-500 dark:text-gray-400">Type structure:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->type_structure_label }}</span></p>
                            <p>
                                <span class="text-gray-500 dark:text-gray-400">Spécialité:</span>
                                @if($dossierProfessionnel->specialite)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                                        {{ $dossierProfessionnel->specialite }}
                                    </span>
                                @else
                                    <span class="font-medium text-gray-900 dark:text-gray-100">—</span>
                                @endif
                            </p>
                            <p><span class="text-gray-500 dark:text-gray-400">Raison sociale:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->raison_sociale ?? '—' }}</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">NIU:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->NIU ?? '—' }}</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Forme juridique:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->forme_juridique ?? '—' }}</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Paiement inscription:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $dossierProfessionnel->statut_paiement_inscription)) }}</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Statut dossier:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->statut_label }}</span></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">Revue automatique IA + regles</h4>
                        <div class="rounded-xl border p-4 text-sm @if(($complianceReview['risk_level'] ?? 'low') === 'high') border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20 @elseif(($complianceReview['risk_level'] ?? 'low') === 'medium') border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20 @else border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20 @endif">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                Risque: {{ strtoupper((string) ($complianceReview['risk_level'] ?? 'low')) }}
                                | Score: {{ (int) ($complianceReview['score'] ?? 0) }}
                                | Source: {{ strtoupper((string) ($complianceReview['source'] ?? 'local')) }}
                            </p>
                            @if(!empty($complianceReview['reasons']))
                                <ul class="mt-2 list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                                    @foreach(($complianceReview['reasons'] ?? []) as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">Pièces fournies</h4>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-700 dark:text-gray-300">Attestation professionnelle</span>
                                @if($dossierProfessionnel->attestation_professionnelle_path)
                                    <a href="{{ Storage::url($dossierProfessionnel->attestation_professionnelle_path) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Ouvrir</a>
                                @else
                                    <span class="text-red-500">Manquante</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-700 dark:text-gray-300">Document de prise de fonction</span>
                                @if($dossierProfessionnel->document_prise_de_fonction_path)
                                    <a href="{{ Storage::url($dossierProfessionnel->document_prise_de_fonction_path) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Ouvrir</a>
                                @else
                                    <span class="text-red-500">Manquant</span>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 p-4 text-xs text-amber-700 dark:text-amber-300">
                            Vérifiez les informations et les pièces avant validation. La validation attribue automatiquement une licence et ouvre l'accès à l'espace de travail professionnel.
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 flex flex-wrap gap-3 justify-end">
                    <a href="{{ route('dossier-professionnels.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Retour</a>

                    @if($dossierProfessionnel->isEnAttente())
                        <form method="POST" action="{{ route('dossier-professionnels.valider', $dossierProfessionnel) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Valider ce dossier et attribuer la licence ?')" class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                                Valider le dossier
                            </button>
                        </form>
                    @endif

                    @if(!$dossierProfessionnel->isRecale())
                        <form method="POST" action="{{ route('dossier-professionnels.recaler', $dossierProfessionnel) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="notes" placeholder="Motif du rejet (optionnel)" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium">Recaler</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
