<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Page 4 - Repertoire des patients</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nom, prenom, telephone, genre, photo et acces rapide.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('professional.workspace.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $patients->count() }} patient(s)</p>
            </div>

            @if($patients->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun patient dans votre repertoire.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($patients as $consultation)
                        @php
                            $dossier = $consultation->dossierMedical;
                            $fullName = trim(($dossier?->prenom ?? '') . ' ' . ($dossier?->nom ?? ''));
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    @if($dossier?->photo_profil_path)
                                        <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}" alt="Photo patient" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-gray-400 text-xs">Photo</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $fullName !== '' ? $fullName : ($consultation->patient?->name ?? 'Patient') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Dossier: {{ $dossier?->numero_unique ?? '—' }}</p>
                                </div>
                            </div>

                            <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                <p>Telephone: {{ $dossier?->telephone ?? '—' }}</p>
                                <p>Genre: {{ $dossier?->sexe ? ucfirst((string) $dossier->sexe) : '—' }}</p>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($dossier)
                                    <a href="{{ route('professional.workspace.patient.dossier', $dossier) }}" class="px-3 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-xs font-medium">Ouvrir dossier</a>
                                @endif
                                <a href="{{ route('professional.workspace.consultation.edit', $consultation) }}" class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium">Consultation-edit</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
