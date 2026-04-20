<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Page 2 - Suivi des patients</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vue dediee pour suivre les patients deja pris en charge.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Patients suivis</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $patientsSuivis->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total consultations</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalConsultations }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</p>
                    <a href="{{ route('professional.workspace.dashboard') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-teal-50 dark:bg-teal-900/10">
                    <h3 class="text-sm font-semibold text-teal-700 dark:text-teal-300 uppercase tracking-wide">Patients et progression</h3>
                </div>
                <div class="p-5">
                    @if($patientsSuivis->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun patient suivi pour le moment.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($patientsSuivis as $entry)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                        <div class="space-y-1 text-sm">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $entry['patient']?->name ?? (($entry['dossier_medical']?->prenom ?? '') . ' ' . ($entry['dossier_medical']?->nom ?? '')) }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-300">Dossier: {{ $entry['dossier_medical']?->numero_unique ?? '—' }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Telephone: {{ $entry['dossier_medical']?->telephone ?? '—' }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Total consultations: {{ $entry['total_consultations'] }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Derniere visite: {{ optional($entry['derniere_visite'])->format('d/m/Y H:i') }}</p>
                                        </div>

                                        <div class="flex flex-col gap-2 w-full lg:w-80">
                                            @if($entry['consultation'])
                                                <a href="{{ route('professional.workspace.consultation.edit', $entry['consultation']) }}" class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium text-center">Continuer le suivi (consultation-edit)</a>
                                            @endif
                                            @if($entry['dossier_medical'])
                                                <a href="{{ route('professional.workspace.patient.dossier', $entry['dossier_medical']) }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-medium text-center">Voir dossier patient</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
