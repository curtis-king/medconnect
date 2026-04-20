<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mes finances</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivi de vos depenses de sante.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <a href="{{ route('patient.payments.index') }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">Voir paiements</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Total depenses</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $totalDepenses, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="rounded-2xl border border-blue-200 dark:border-blue-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300">Consultations</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $depensesConsultations, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="rounded-2xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300">Examens</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $depensesExamens, 0, ',', ' ') }} XAF</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Evolution mensuelle</h3>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse($depensesMensuelles as $month => $amount)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $month }}</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format((float) $amount, 0, ',', ' ') }} XAF</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune depense enregistree.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Dernieres depenses payees</h3>
                <div class="mt-3 space-y-2">
                    @forelse($depensesPayees->take(20) as $depense)
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 text-xs">
                            <span class="text-gray-700 dark:text-gray-300">{{ $depense->reference }} - {{ $depense->serviceProfessionnel?->nom ?? ucfirst((string) $depense->type_facture) }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format((float) $depense->montant_total, 0, ',', ' ') }} XAF</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune depense payee.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
