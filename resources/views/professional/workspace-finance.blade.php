<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Page 3 - Suivi financier et retraits</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Transactions, statuts de factures et demande de retrait vers le backoffice.</p>
            @if(($backofficeFeedbackUnreadCount ?? 0) > 0)
                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700 text-xs font-medium">
                    <span class="inline-flex w-2 h-2 rounded-full bg-amber-500"></span>
                    Nouveau retour backoffice: {{ $backofficeFeedbackUnreadCount }} non lu(s)
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total facture</p>
                    <p class="mt-2 text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $financeStats['total_facture'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total paye (net)</p>
                    <p class="mt-2 text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ number_format((float) $financeStats['factures_payees_net'], 0, ',', ' ') }} XAF</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Brut: {{ number_format((float) $financeStats['factures_payees'], 0, ',', ' ') }} XAF | Retraits traites: {{ number_format((float) $financeStats['retraits_traites'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Encours backoffice</p>
                    <p class="mt-2 text-xl font-bold text-amber-700 dark:text-amber-300">{{ number_format((float) $financeStats['encours_backoffice'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Solde retirable</p>
                    <p class="mt-2 text-xl font-bold text-cyan-700 dark:text-cyan-300">{{ number_format((float) $financeStats['solde_retirable'], 0, ',', ' ') }} XAF</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Demander un retrait au backoffice</h3>
                <form method="POST" action="{{ route('professional.workspace.finance.withdrawal.request') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant demande (XAF)</label>
                        <input type="number" name="montant_demande" required min="1000" step="100"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                               placeholder="10000">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optionnel)</label>
                        <input type="text" name="notes"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                               placeholder="Commentaire pour le backoffice">
                    </div>
                    <div class="md:col-span-3 flex items-center justify-between">
                        <a href="{{ route('professional.workspace.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium">Envoyer la demande</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-cyan-50 dark:bg-cyan-900/10">
                    <h3 class="text-sm font-semibold text-cyan-700 dark:text-cyan-300 uppercase tracking-wide">Transactions facture</h3>
                </div>
                <div class="p-5">
                    @if($factures->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune facture disponible.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">Reference</th>
                                        <th class="py-2 pr-4">Patient</th>
                                        <th class="py-2 pr-4">Montant</th>
                                        <th class="py-2 pr-4">Paiement patient</th>
                                        <th class="py-2 pr-4">Backoffice</th>
                                        <th class="py-2 pr-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($factures as $facture)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-3 pr-4 text-gray-900 dark:text-gray-100">{{ $facture->reference }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $facture->patient?->name ?? (($facture->dossierMedical?->prenom ?? '') . ' ' . ($facture->dossierMedical?->nom ?? '')) }}</td>
                                            <td class="py-3 pr-4 text-gray-700 dark:text-gray-200">{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} XAF</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ ucfirst((string) $facture->statut_paiement_patient) }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">
                                                @php
                                                    $backofficeLabel = match ((string) $facture->statut_backoffice) {
                                                        'en_attente' => 'En attente backoffice',
                                                        'valide' => 'Valide par backoffice',
                                                        'rejete' => 'Rejete par backoffice',
                                                        'paye' => 'Paye par backoffice',
                                                        default => ucfirst((string) $facture->statut_backoffice),
                                                    };
                                                @endphp
                                                {{ $backofficeLabel }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="{{ route('professional.workspace.facture.print', $facture) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Imprimer</a>
                                                    @if($facture->statut_paiement_patient !== 'paye')
                                                        <form method="POST" action="{{ route('professional.workspace.finance.invoice.mark-paid', $facture) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium">Paiement physique</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $factures->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-violet-50 dark:bg-violet-900/10">
                    <h3 class="text-sm font-semibold text-violet-700 dark:text-violet-300 uppercase tracking-wide">Historique des demandes de retrait</h3>
                </div>
                <div class="p-5">
                    @if($retraits->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune demande de retrait.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($retraits as $retrait)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-sm">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $retrait->reference }} - {{ number_format((float) $retrait->montant_demande, 0, ',', ' ') }} XAF</p>
                                    <p class="text-gray-600 dark:text-gray-300">Statut: {{ ucfirst((string) $retrait->statut) }} | Factures associees: {{ $retrait->factures_count }}</p>
                                    <p class="text-gray-600 dark:text-gray-300">Demande le: {{ optional($retrait->date_demande)->format('d/m/Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
