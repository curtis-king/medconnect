<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dossier Professionnel — {{ $dossierProfessionnel->user->name ?? 'N/A' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- En-tête statut + actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            @if($dossierProfessionnel->image_identite_path)
                                <img src="{{ Storage::url($dossierProfessionnel->image_identite_path) }}" alt="Visuel professionnel" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            @endif
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $dossierProfessionnel->raison_sociale ?? $dossierProfessionnel->user->name ?? 'Dossier Professionnel' }}
                            </h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($dossierProfessionnel->statut === 'valide') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($dossierProfessionnel->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ $dossierProfessionnel->statut_label }}
                            </span>
                        </div>
                        @if($dossierProfessionnel->numero_licence)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Licence : <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $dossierProfessionnel->numero_licence }}</span>
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dossier-professionnels.edit', $dossierProfessionnel) }}"
                           class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm transition">
                            Modifier
                        </a>
                        @if($dossierProfessionnel->isEnAttente())
                            <form method="POST" action="{{ route('dossier-professionnels.valider', $dossierProfessionnel) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Valider ce dossier et attribuer une licence ?')"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                    ✓ Valider
                                </button>
                            </form>
                        @endif
                        @if(!$dossierProfessionnel->isRecale())
                            <button type="button" onclick="document.getElementById('recalerModal').classList.remove('hidden')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                ✗ Recaler
                            </button>
                        @endif
                        @if($dossierProfessionnel->isRecale() || $dossierProfessionnel->isValide())
                            <form method="POST" action="{{ route('dossier-professionnels.remettre', $dossierProfessionnel) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    Remettre en attente
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informations professionnelles -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Informations Professionnelles</h4>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Utilisateur</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->user->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->user->email ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Raison Sociale</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->raison_sociale ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Type de Structure</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->type_structure_label }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Spécialité</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->specialite ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">NIU</dt>
                            <dd class="font-medium font-mono text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->NIU ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Forme Juridique</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->forme_juridique ?? '—' }}</dd>
                        </div>
                        @if($dossierProfessionnel->notes)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 mb-1">Notes</dt>
                            <dd class="text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-2 rounded">{{ $dossierProfessionnel->notes }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Informations paiement inscription -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Inscription & Abonnement</h4>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Tarif d'inscription</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $dossierProfessionnel->frais?->libelle ?? '—' }}
                                @if($dossierProfessionnel->frais)
                                    ({{ number_format($dossierProfessionnel->frais->prix, 0, ',', ' ') }} XAF)
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Statut Paiement</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($dossierProfessionnel->statut_paiement_inscription === 'paye') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($dossierProfessionnel->statut_paiement_inscription === 'exonere') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $dossierProfessionnel->statut_paiement_inscription)) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Mode de Paiement</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $dossierProfessionnel->mode_paiement_inscription ?? '—')) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Abonnement actif</dt>
                            <dd>
                                @if($dossierProfessionnel->hasActiveSubscription())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Actif jusqu'au {{ $dossierProfessionnel->activeSubscription?->date_fin?->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Expiré / Aucun</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('subscriptions-pro.index', $dossierProfessionnel) }}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                            → Voir les abonnements
                        </a>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            @if($dossierProfessionnel->attestation_professionnelle_path || $dossierProfessionnel->document_prise_de_fonction_path)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Documents</h4>
                <div class="flex flex-wrap gap-4">
                    @if($dossierProfessionnel->attestation_professionnelle_path)
                        <a href="{{ Storage::url($dossierProfessionnel->attestation_professionnelle_path) }}" target="_blank"
                           class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-4 py-3 rounded-lg text-sm transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Attestation Professionnelle
                        </a>
                    @endif
                    @if($dossierProfessionnel->document_prise_de_fonction_path)
                        <a href="{{ Storage::url($dossierProfessionnel->document_prise_de_fonction_path) }}" target="_blank"
                           class="flex items-center gap-2 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300 px-4 py-3 rounded-lg text-sm transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Document Prise de Fonction
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Services -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Services ({{ $dossierProfessionnel->services->count() }})</h4>
                    <a href="{{ route('services-pro.create', $dossierProfessionnel) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs transition">
                        + Ajouter un service
                    </a>
                </div>
                @if($dossierProfessionnel->services->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Aucun service enregistré.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nom</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Prix</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($dossierProfessionnel->services as $service)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $service->nom }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $service->type_label }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $service->montant_formatted }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $service->actif ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                            {{ $service->actif ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 flex items-center gap-2">
                                        <a href="{{ route('services-pro.edit', [$dossierProfessionnel, $service]) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs">Modifier</a>
                                        <form method="POST" action="{{ route('services-pro.toggle-actif', [$dossierProfessionnel, $service]) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs">
                                                {{ $service->actif ? 'Désactiver' : 'Activer' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('services-pro.destroy', [$dossierProfessionnel, $service]) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs"
                                                    onclick="return confirm('Supprimer ce service ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="flex justify-start">
                <a href="{{ route('dossier-professionnels.index') }}"
                   class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                    ← Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Recaler -->
    <div id="recalerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Recaler le dossier</h3>
            <form method="POST" action="{{ route('dossier-professionnels.recaler', $dossierProfessionnel) }}">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif du recalage (optionnel)</label>
                    <textarea name="notes" rows="4"
                              placeholder="Expliquez pourquoi le dossier est recalé..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('recalerModal').classList.add('hidden')"
                            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        Confirmer le recalage
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
