<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Prise en charge {{ $soumission->reference }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Validation, rejet ou paiement de la demande soumise par le patient.</p>
            </div>
            <a href="{{ route('admin.soumissions-mutuelle.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour liste</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    <ul class="space-y-1 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Montant soumis</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $soumission->montant_soumis, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pris en charge</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format((float) $soumission->montant_pris_en_charge, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Montant rejete</p>
                    <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">{{ number_format((float) $soumission->montant_rejete, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Backoffice</p>
                    <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ ucfirst(str_replace('_', ' ', (string) $soumission->factureProfessionnelle?->statut_backoffice)) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resume de la demande</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Patient</p>
                                <p class="mt-1 font-medium">{{ $soumission->dossierMedical?->user?->name ?? 'N/A' }}</p>
                                <p>{{ $soumission->dossierMedical?->user?->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Professionnel</p>
                                <p class="mt-1 font-medium">{{ $soumission->factureProfessionnelle?->dossierProfessionnel?->user?->name ?? 'N/A' }}</p>
                                <p>{{ $soumission->factureProfessionnelle?->serviceProfessionnel?->nom ?? 'Service non renseigne' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Facture</p>
                                <p class="mt-1 font-medium">{{ $soumission->factureProfessionnelle?->reference ?? 'N/A' }}</p>
                                <p>Total: {{ number_format((float) ($soumission->factureProfessionnelle?->montant_total ?? 0), 0, ',', ' ') }} XAF</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Abonnement</p>
                                <p class="mt-1 font-medium">{{ $soumission->subscription?->reference_paiement ?? 'Aucune reference' }}</p>
                                <p>Soumis le {{ $soumission->date_soumission?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-900/40 border border-slate-200 dark:border-gray-700">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Statut demande</p>
                                <p class="mt-2 font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', (string) $soumission->statut)) }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-900/40 border border-slate-200 dark:border-gray-700">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Statut facture</p>
                                <p class="mt-2 font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', (string) $soumission->factureProfessionnelle?->statut_mutuelle)) }}</p>
                            </div>
                        </div>

                        @if(filled($soumission->motif_rejet))
                            <div class="mt-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800">
                                <p class="text-sm font-medium text-rose-800 dark:text-rose-300">Motif de rejet</p>
                                <p class="mt-2 text-sm text-rose-700 dark:text-rose-200">{{ $soumission->motif_rejet }}</p>
                            </div>
                        @endif

                        @if(filled($soumission->notes))
                            <div class="mt-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Notes admin / backoffice</p>
                                <p class="mt-2 text-sm text-blue-700 dark:text-blue-200 whitespace-pre-line">{{ $soumission->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 h-fit">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Action admin</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Passez la demande en traitement, validez-la, rejetez-la ou marquez le paiement effectue.</p>

                    <form method="POST" action="{{ route('admin.soumissions-mutuelle.update', $soumission) }}" class="mt-5 space-y-4" id="soumission-action-form">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Action</label>
                            <select id="action" name="action" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                                @php
                                    $selectedAction = old('action', 'processing');
                                @endphp
                                <option value="processing" @selected($selectedAction === 'processing')>Mettre en traitement</option>
                                <option value="approve" @selected($selectedAction === 'approve')>Valider la prise en charge</option>
                                <option value="reject" @selected($selectedAction === 'reject')>Rejeter la demande</option>
                                <option value="pay" @selected($selectedAction === 'pay')>Marquer comme payee</option>
                            </select>
                        </div>

                        <div id="covered-amount-block">
                            <label for="montant_pris_en_charge" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Montant pris en charge</label>
                            <input id="montant_pris_en_charge" name="montant_pris_en_charge" type="number" min="0" step="0.01" value="{{ old('montant_pris_en_charge', (float) $soumission->montant_soumis) }}" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum: {{ number_format((float) $soumission->montant_soumis, 0, ',', ' ') }} XAF</p>
                        </div>

                        <div id="rejection-reason-block" class="hidden">
                            <label for="motif_rejet" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Motif de rejet</label>
                            <textarea id="motif_rejet" name="motif_rejet" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">{{ old('motif_rejet', $soumission->motif_rejet) }}</textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Notes</label>
                            <textarea id="notes" name="notes" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">{{ old('notes', $soumission->notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Enregistrer l action</button>
                    </form>

                    <div class="mt-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-200">
                        Valider met la facture en statut backoffice valide. Marquer payee cloture le paiement backoffice vers le professionnel.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const actionInput = document.getElementById('action');
            const coveredAmountBlock = document.getElementById('covered-amount-block');
            const rejectionReasonBlock = document.getElementById('rejection-reason-block');

            const syncBlocks = function () {
                if (!actionInput || !coveredAmountBlock || !rejectionReasonBlock) {
                    return;
                }

                const action = actionInput.value;
                coveredAmountBlock.classList.toggle('hidden', action !== 'approve');
                rejectionReasonBlock.classList.toggle('hidden', action !== 'reject' && action !== 'approve');
            };

            if (actionInput) {
                actionInput.addEventListener('change', syncBlocks);
                syncBlocks();
            }
        });
    </script>
</x-app-layout>
