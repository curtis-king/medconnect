<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Rapports d'optimisation IA</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Historique des analyses de plateforme générées par l'IA</p>
            </div>
            <a href="{{ route('admin.optimization-reports.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                Générer nouveau rapport
            </a>
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @forelse($reports as $report)
                    <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.optimization-reports.show', $report) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                        Rapport {{ $report->generated_at->format('d/m/Y H:i') }}
                                    </a>
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $report->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Provider: <span class="font-medium">{{ strtoupper($report->provider) }}</span> |
                                    Seuils: {{ $report->stale_invoice_days }}j / {{ $report->stale_backoffice_days }}j / {{ $report->upcoming_window_days }}j
                                </p>
                                @php
                                    $actionStatusClasses = match ($report->action_status) {
                                        'done' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'blocked' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    };
                                @endphp
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-block px-2 py-1 rounded-full {{ $actionStatusClasses }}">
                                        Action: {{ str_replace('_', ' ', ucfirst((string) ($report->action_status ?? 'pending'))) }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Échéance: {{ $report->action_due_date?->format('d/m/Y') ?? 'Non définie' }}
                                    </span>
                                </div>
                                @if(filled($report->admin_response))
                                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                        Retour: {{ $report->admin_response }}
                                    </p>
                                @endif
                                @if($report->metrics)
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Factures en attente</p>
                                            <p class="mt-1 text-lg font-bold text-red-600 dark:text-red-400">{{ $report->metrics['invoices']['pending_total_count'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">RDV en retard</p>
                                            <p class="mt-1 text-lg font-bold text-orange-600 dark:text-orange-400">{{ $report->metrics['appointments']['past_due_count'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Soumis en retard</p>
                                            <p class="mt-1 text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $report->metrics['backoffice']['delayed_submission_count'] ?? 0 }}</p>
                                        </div>
                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                            <p class="text-xs text-gray-600 dark:text-gray-400">RDV à venir</p>
                                            <p class="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400">{{ $report->metrics['appointments']['upcoming_count'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('admin.optimization-reports.show', $report) }}" class="px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">
                                    Détails
                                </a>
                                <form method="POST" action="{{ route('admin.optimization-reports.destroy', $report) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" onclick="return confirm('Confirmer suppression?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">Aucun rapport généré. <a href="{{ route('admin.optimization-reports.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Générer un rapport</a></p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
