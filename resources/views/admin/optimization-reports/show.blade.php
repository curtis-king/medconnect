<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Rapport IA - {{ $report->generated_at->format('d/m/Y H:i') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Analyse détaillée avec recommandations prioritaires</p>
            </div>
            <a href="{{ route('admin.optimization-reports.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour liste</a>
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
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Provider</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($report->provider) }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Généré le</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->generated_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Statut</p>
                    <p class="mt-1 text-lg font-semibold">
                        <span class="inline-block px-2 py-1 rounded-full text-sm {{ $report->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Actualisation</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Statut d'action</p>
                    @php
                        $statusClasses = match ($report->action_status) {
                            'done' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'blocked' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        };
                    @endphp
                    <p class="mt-2">
                        <span class="inline-block px-2 py-1 rounded-full text-sm {{ $statusClasses }}">
                            {{ str_replace('_', ' ', ucfirst((string) $report->action_status)) }}
                        </span>
                    </p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Date d'échéance action</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $report->action_due_date?->format('d/m/Y') ?? 'Non définie' }}
                    </p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Action terminée le</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $report->action_completed_at?->format('d/m/Y H:i') ?? 'Pas encore' }}
                    </p>
                </div>
            </div>

            @if($report->metrics)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Métriques collectées</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Factures</h4>
                            <div class="space-y-2">
                                <p class="text-sm">En attente: <span class="font-bold">{{ $report->metrics['invoices']['pending_total_count'] ?? 0 }}</span> ({{ number_format($report->metrics['invoices']['pending_total_amount_xaf'] ?? 0, 0, ',', ' ') }} XAF)</p>
                                <p class="text-sm">Obsolètes (>{{ $report->stale_invoice_days }}j): <span class="font-bold">{{ $report->metrics['invoices']['stale_count'] ?? 0 }}</span> ({{ number_format($report->metrics['invoices']['stale_amount_xaf'] ?? 0, 0, ',', ' ') }} XAF)</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Plus ancienne: {{ $report->metrics['invoices']['oldest_pending_date'] ? \Carbon\Carbon::parse($report->metrics['invoices']['oldest_pending_date'])->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Rendez-vous</h4>
                            <div class="space-y-2">
                                <p class="text-sm">En retard: <span class="font-bold">{{ $report->metrics['appointments']['past_due_count'] ?? 0 }}</span></p>
                                <p class="text-sm">À venir ({{ $report->upcoming_window_days }}j): <span class="font-bold">{{ $report->metrics['appointments']['upcoming_count'] ?? 0 }}</span></p>
                            </div>
                        </div>
                        <div class="space-y-3 md:col-span-2">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Backoffice</h4>
                            <div class="space-y-2">
                                <p class="text-sm">Soumissions en retard (>{{ $report->stale_backoffice_days }}j): <span class="font-bold">{{ $report->metrics['backoffice']['delayed_submission_count'] ?? 0 }}</span> ({{ number_format($report->metrics['backoffice']['delayed_submission_amount_xaf'] ?? 0, 0, ',', ' ') }} XAF)</p>
                                <p class="text-sm">Nouvelles demandes soumises: <span class="font-bold">{{ $report->metrics['backoffice']['submitted_count'] ?? 0 }}</span></p>
                                <p class="text-sm">Demandes en traitement: <span class="font-bold">{{ $report->metrics['backoffice']['processing_count'] ?? 0 }}</span></p>
                                <p class="text-sm">Demandes validees en attente de paiement: <span class="font-bold">{{ $report->metrics['backoffice']['approved_waiting_payment_count'] ?? 0 }}</span></p>
                                <p class="text-sm">Demandes rejetees: <span class="font-bold">{{ $report->metrics['backoffice']['rejected_count'] ?? 0 }}</span></p>
                                <p class="text-sm">Demandes payees: <span class="font-bold">{{ $report->metrics['backoffice']['paid_count'] ?? 0 }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
*** Add File: c:\laragon\www\zer\resources\views\admin\soumissions-mutuelle\index.blade.php
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Demandes de prise en charge sante</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestion admin des demandes soumises par les patients: validation, rejet et paiement.</p>
            </div>
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

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Soumises</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['submitted_count'] }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">En traitement</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['processing_count'] }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Validees a payer</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['approved_waiting_payment_count'] }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Payees</p>
                    <p class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['paid_count'] }}</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rejetees</p>
                    <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $stats['rejected_count'] }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.soumissions-mutuelle.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Recherche</label>
                    <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Reference, patient, email" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Statut demande</label>
                    <select id="statut" name="statut" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                        <option value="">Tous</option>
                        @foreach(['soumis' => 'Soumis', 'en_traitement' => 'En traitement', 'approuve' => 'Approuve', 'partiel' => 'Partiel', 'rejete' => 'Rejete'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="backoffice_status" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Statut backoffice</label>
                    <select id="backoffice_status" name="backoffice_status" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                        <option value="">Tous</option>
                        @foreach(['en_attente' => 'En attente', 'valide' => 'Valide', 'rejete' => 'Rejete', 'paye' => 'Paye'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['backoffice_status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Filtrer</button>
                    <a href="{{ route('admin.soumissions-mutuelle.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-lg text-sm font-medium">Reinitialiser</a>
                </div>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Reference</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Facture</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Demande</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Backoffice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($soumissions as $soumission)
                                @php
                                    $demandeClasses = match ($soumission->statut) {
                                        'soumis' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        'en_traitement' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'approuve', 'partiel' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'rejete' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                                    };
                                    $backofficeClasses = match ($soumission->factureProfessionnelle?->statut_backoffice) {
                                        'valide' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'paye' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                        'rejete' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                                        default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $soumission->reference }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div>{{ $soumission->dossierMedical?->user?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $soumission->dossierMedical?->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div>{{ $soumission->factureProfessionnelle?->reference ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $soumission->factureProfessionnelle?->dossierProfessionnel?->user?->name ?? 'Professionnel N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">{{ number_format((float) $soumission->montant_soumis, 0, ',', ' ') }} XAF</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $demandeClasses }}">{{ ucfirst(str_replace('_', ' ', (string) $soumission->statut)) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $backofficeClasses }}">{{ ucfirst(str_replace('_', ' ', (string) ($soumission->factureProfessionnelle?->statut_backoffice ?? 'en_attente'))) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $soumission->date_soumission?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('admin.soumissions-mutuelle.show', $soumission) }}" class="inline-flex px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">Traiter</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Aucune demande de prise en charge trouvee.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $soumissions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📋 Rapport d'analyse IA</h3>
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <div class="text-gray-700 dark:text-gray-300 space-y-4">
                        {!! nl2br(e($report->content)) !!}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Retour admin et plan d'action</h3>
                <form method="POST" action="{{ route('admin.optimization-reports.update-action', $report) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="admin_response" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Réponse / Retour</label>
                        <textarea id="admin_response" name="admin_response" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2" placeholder="Ex: Nous validons l'analyse, équipe finance assignée ce matin.">{{ old('admin_response', $report->admin_response) }}</textarea>
                    </div>

                    <div>
                        <label for="action_plan" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Action à exécuter</label>
                        <textarea id="action_plan" name="action_plan" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2" placeholder="Ex: 1) Contacter les 3 patients en retard 2) Traiter les factures aujourd'hui.">{{ old('action_plan', $report->action_plan) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="action_status" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Statut de l'action</label>
                            <select id="action_status" name="action_status" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2">
                                @php
                                    $selectedStatus = old('action_status', $report->action_status ?? 'pending');
                                @endphp
                                <option value="pending" @selected($selectedStatus === 'pending')>En attente</option>
                                <option value="in_progress" @selected($selectedStatus === 'in_progress')>En cours</option>
                                <option value="done" @selected($selectedStatus === 'done')>Terminé</option>
                                <option value="blocked" @selected($selectedStatus === 'blocked')>Bloqué</option>
                            </select>
                        </div>
                        <div>
                            <label for="action_due_date" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Date limite</label>
                            <input id="action_due_date" name="action_due_date" type="date" value="{{ old('action_due_date', $report->action_due_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">Enregistrer retour et action</button>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Le statut Terminé marque automatiquement la date de clôture.</p>
                    </div>
                </form>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.optimization-reports.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    Générer nouveau rapport
                </a>
                <form method="POST" action="{{ route('admin.optimization-reports.destroy', $report) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium" onclick="return confirm('Confirmer suppression?')">
                        Supprimer ce rapport
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
