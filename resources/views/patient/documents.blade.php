<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mes documents medicaux</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensemble de vos fichiers recus: consultations, examens, resultats, ordonnances.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $documents->count() }}</span>
            </div>

            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-5 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Assistant IA de suivi du traitement</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Analyse l ordonnance et la prescription pour estimer la duree du traitement, les jours a suivre et les points de vigilance.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ session('ordonnance_ai_analysis.source') === 'assistant_ia' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                        {{ session('ordonnance_ai_analysis.source') === 'assistant_ia' ? 'IA active' : 'Fallback local' }}
                    </span>
                </div>

                @if($ordonnances->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune ordonnance structurée disponible pour l analyse.</p>
                @else
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @foreach($ordonnances as $ordonnance)
                            @php
                                $isActiveAnalysis = (int) session('ordonnance_ai_analysis_id') === (int) $ordonnance->id;
                                $analysis = $isActiveAnalysis ? session('ordonnance_ai_analysis') : null;
                            @endphp
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 p-4 space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Ordonnance #{{ $ordonnance->id }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ optional($ordonnance->created_at)->format('d/m/Y H:i') }} · {{ $ordonnance->professionnel?->name ?? 'Professionnel' }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('patient.documents.ordonnance.analyze', $ordonnance) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Analyser</button>
                                    </form>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                    @if(collect($ordonnance->produits ?? [])->isNotEmpty())
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Traitements</p>
                                            <ul class="mt-2 space-y-1">
                                                @foreach($ordonnance->produits as $produit)
                                                    <li class="rounded-lg bg-gray-50 dark:bg-gray-900/40 px-3 py-2">{{ $produit }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($ordonnance->prescription)
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Prescription</p>
                                            <p class="mt-1 whitespace-pre-line">{{ $ordonnance->prescription }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($analysis)
                                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-700 bg-emerald-50/60 dark:bg-emerald-900/10 p-4 space-y-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Lecture assistée du traitement</p>
                                            <span class="text-xs text-emerald-700 dark:text-emerald-400">{{ $analysis['duree_estimee_jours'] ? $analysis['duree_estimee_jours'].' jour(s)' : 'Durée à confirmer' }}</span>
                                        </div>

                                        <p class="text-sm text-emerald-900 dark:text-emerald-100">{{ $analysis['resume'] ?? 'Résumé indisponible.' }}</p>

                                        @if(!empty($analysis['periode_resume']))
                                            <p class="text-xs text-emerald-700 dark:text-emerald-400">{{ $analysis['periode_resume'] }}</p>
                                        @endif

                                        @if(!empty($analysis['prises']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Détail traitement</p>
                                                <div class="mt-2 space-y-2">
                                                    @foreach($analysis['prises'] as $prise)
                                                        <div class="rounded-lg bg-white/80 dark:bg-gray-950/30 px-3 py-2">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $prise['medicament'] ?? 'Traitement' }}</p>
                                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $prise['consigne'] ?? 'Consigne non détaillée.' }}</p>
                                                            @if(!empty($prise['duree_texte']) || !empty($prise['duree_jours']))
                                                                <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">{{ $prise['duree_texte'] ?? ($prise['duree_jours'].' jour(s)') }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($analysis['conseils']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Conseils de suivi</p>
                                                <ul class="mt-2 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                                                    @foreach($analysis['conseils'] as $conseil)
                                                        <li>• {{ $conseil }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if(!empty($analysis['points_attention']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Points d attention</p>
                                                <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-200">
                                                    @foreach($analysis['points_attention'] as $point)
                                                        <li>• {{ $point }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-cyan-200 dark:border-cyan-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="space-y-3">
                    @forelse($documents as $document)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $document['titre'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Type: {{ str_replace('_', ' ', (string) $document['type']) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Date: {{ optional($document['date'])->format('d/m/Y H:i') }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="px-3 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium">Ouvrir</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun document medical disponible.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
