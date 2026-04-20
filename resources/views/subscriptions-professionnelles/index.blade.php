<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Abonnements Professionnels — {{ $dossierProfessionnel->raison_sociale ?? $dossierProfessionnel->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Historique des Abonnements</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Licence : <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $dossierProfessionnel->numero_licence ?? '—' }}</span>
                            </p>
                        </div>
                        <a href="{{ route('subscriptions-pro.create', $dossierProfessionnel) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            + Nouvel Abonnement
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Durée</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($subscriptions as $sub)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div>{{ $sub->date_debut->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">→ {{ $sub->date_fin->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $sub->nombre_mois }} mois
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format((float) $sub->montant, 0, ',', ' ') }} XAF
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ ucfirst(str_replace('_', ' ', $sub->mode_paiement ?? '—')) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($sub->statut === 'actif' && !$sub->isExpired()) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($sub->statut === 'annule') bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                            {{ $sub->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($sub->statut === 'actif')
                                            <form method="POST" action="{{ route('subscriptions-pro.cancel', [$dossierProfessionnel, $sub]) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs"
                                                        onclick="return confirm('Annuler cet abonnement ?')">Annuler</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        Aucun abonnement trouvé.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $subscriptions->links() }}</div>
                </div>
            </div>

            <div class="flex justify-start">
                <a href="{{ route('dossier-professionnels.show', $dossierProfessionnel) }}"
                   class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                    ← Retour au dossier
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
