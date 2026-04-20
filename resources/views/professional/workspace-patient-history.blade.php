<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Page 5 - Historique complet patient</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Toutes les consultations avec acces direct a consultation-edit.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                <form method="GET" action="{{ route('professional.workspace.patients.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-3">
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Recherche: nom, prenom, dossier, telephone"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium">Rechercher</button>
                        <a href="{{ route('professional.workspace.patients.history') }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-100 text-sm">Reset</a>
                    </div>
                </form>
                <div class="mt-3">
                    <a href="{{ route('professional.workspace.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Historique consultations</h3>
                </div>
                <div class="p-5">
                    @if($consultations->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun historique pour cette recherche.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">Date</th>
                                        <th class="py-2 pr-4">Patient</th>
                                        <th class="py-2 pr-4">Dossier</th>
                                        <th class="py-2 pr-4">Service</th>
                                        <th class="py-2 pr-4">Statut</th>
                                        <th class="py-2 pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consultations as $consultation)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ optional($consultation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="py-3 pr-4 text-gray-900 dark:text-gray-100">{{ $consultation->patient?->name ?? (($consultation->dossierMedical?->prenom ?? '') . ' ' . ($consultation->dossierMedical?->nom ?? '')) }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $consultation->numero_dossier_reference ?? $consultation->dossierMedical?->numero_unique ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $consultation->rendezVous?->serviceProfessionnel?->nom ?? ucfirst((string) $consultation->type_service) }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ ucfirst((string) $consultation->statut) }}</td>
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('professional.workspace.consultation.edit', $consultation) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Ouvrir consultation-edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $consultations->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
