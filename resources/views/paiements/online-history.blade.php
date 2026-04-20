<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Historique des paiements en ligne
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paiements en ligne - Dossiers médicaux</h3>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Utilisateur</th>
                                <th class="px-4 py-2 text-left">Dossier</th>
                                <th class="px-4 py-2 text-left">Montant</th>
                                <th class="px-4 py-2 text-left">Mode</th>
                                <th class="px-4 py-2 text-left">Référence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($medicalOnlinePayments as $payment)
                                <tr>
                                    <td class="px-4 py-2">{{ optional($payment->date_encaissement)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $payment->dossierMedical?->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $payment->dossierMedical?->numero_unique ?? '-' }}</td>
                                    <td class="px-4 py-2 font-medium">{{ number_format((float) $payment->montant, 0, ',', ' ') }} XAF</td>
                                    <td class="px-4 py-2">{{ ucfirst((string) $payment->mode_paiement) }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $payment->reference_paiement }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun paiement en ligne médical trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $medicalOnlinePayments->links() }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paiements en ligne - Dossiers professionnels</h3>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Utilisateur</th>
                                <th class="px-4 py-2 text-left">Structure</th>
                                <th class="px-4 py-2 text-left">Montant</th>
                                <th class="px-4 py-2 text-left">Mode</th>
                                <th class="px-4 py-2 text-left">Référence</th>
                                <th class="px-4 py-2 text-left">Statut dossier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($professionalOnlinePayments as $dossier)
                                <tr>
                                    <td class="px-4 py-2">{{ optional($dossier->encaisse_le)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $dossier->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $dossier->raison_sociale ?? $dossier->user?->name ?? '-' }}</td>
                                    <td class="px-4 py-2 font-medium">{{ number_format((float) ($dossier->frais?->prix ?? 0), 0, ',', ' ') }} XAF</td>
                                    <td class="px-4 py-2">{{ ucfirst((string) $dossier->mode_paiement_inscription) }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $dossier->reference_paiement_inscription }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($dossier->statut === 'valide') bg-green-100 text-green-800
                                            @elseif($dossier->statut === 'en_attente') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', (string) $dossier->statut)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucun paiement en ligne professionnel trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $professionalOnlinePayments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
