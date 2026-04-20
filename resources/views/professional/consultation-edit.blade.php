<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestion de la consultation</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consultation · Ordonnance · Examens · Suivi · HAD · Documents</p>
        </div>
    </x-slot>

    @php
        $activeTab = old('_active_tab', 'consultation');
        $allowedTabs = ['consultation', 'ordonnances', 'examens', 'suivi', 'had', 'documents'];
        $initialTab = in_array($activeTab, $allowedTabs, true) ? $activeTab : 'consultation';

        $patientPhoneRaw = $consultation->dossierMedical?->telephone ?? $consultation->patient?->phone ?? '';
        $patientPhoneDigits = preg_replace('/\D+/', '', (string) $patientPhoneRaw);
        if (str_starts_with($patientPhoneDigits, '00')) {
            $patientPhoneDigits = substr($patientPhoneDigits, 2);
        }

        $consultationPrintUrl = route('professional.workspace.consultation.print', $consultation, true);
        $summaryPrintUrl = route('professional.workspace.consultation.summary.print', $consultation, true);
        $resultFileUrl = $consultation->fichier_resultat_path ? url(Storage::url($consultation->fichier_resultat_path)) : null;

        $patientWhatsappUrl = null;
        $patientWhatsappShareConsultationUrl = null;
        $patientWhatsappShareSummaryUrl = null;
        $patientWhatsappShareResultUrl = null;

        if ($patientPhoneDigits !== '') {
            $patientWhatsappUrl = 'https://wa.me/' . $patientPhoneDigits;

            $baseGreeting = 'Bonjour ' . ($consultation->patient?->name ?? '') . ', ';
            $patientWhatsappShareConsultationUrl = $patientWhatsappUrl . '?text=' . urlencode($baseGreeting . 'vous pouvez consulter le compte-rendu imprimable ici : ' . $consultationPrintUrl);
            $patientWhatsappShareSummaryUrl = $patientWhatsappUrl . '?text=' . urlencode($baseGreeting . 'voici la synthese complete de votre consultation : ' . $summaryPrintUrl);

            if ($resultFileUrl) {
                $patientWhatsappShareResultUrl = $patientWhatsappUrl . '?text=' . urlencode($baseGreeting . 'votre fichier de resultat est disponible ici : ' . $resultFileUrl);
            }
        }

        $professionalsForJs = $professionnelsExamensExternes->map(function ($dossier) {
            return [
                'id' => $dossier->id,
                'name' => $dossier->user?->name,
                'specialite' => $dossier->specialite,
                'city' => $dossier->user?->city,
                'services' => $dossier->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'nom' => $service->nom,
                        'prix' => number_format((float) $service->prix, 0, ',', ' ') . ' XAF',
                    ];
                })->values(),
            ];
        })->values();
    @endphp

    <div class="py-10"
        x-data="{
            tab: @js($initialTab),
            orientationMode: @js(old('examen_mode_orientation', 'interne')),
            searchExternal: '',
            selectedExternal: null,
            professionals: @js($professionalsForJs),
            get filteredProfessionals() {
                const search = this.searchExternal.toLowerCase().trim();
                const sorted = [...this.professionals].sort((a, b) => (a.specialite || '').localeCompare(b.specialite || ''));

                if (search === '') {
                    return sorted;
                }

                return sorted.filter((pro) => {
                    const haystack = `${pro.name || ''} ${pro.specialite || ''} ${pro.city || ''}`.toLowerCase();
                    return haystack.includes(search);
                });
            },
            selectProfessional(pro) {
                this.selectedExternal = pro;
            },
        }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

                {{-- Patient / RDV Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-5 text-white">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-bold">{{ $consultation->patient->name ?? '—' }}</p>
                            <p class="text-sm text-blue-100 mt-0.5">
                                RDV: {{ optional($consultation->rendezVous?->date_proposee)->format('d/m/Y H:i') }}
                                &nbsp;·&nbsp; Service: {{ $consultation->serviceProfessionnel?->nom ?? ucfirst($consultation->type_service) }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @php $mode = $consultation->rendezVous?->mode_deroulement ?? 'presentiel'; @endphp
                            @if($mode === 'teleconsultation')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-500/60 border border-blue-300/40 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Téléconsultation
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500/60 border border-green-300/40 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Présentiel
                                </span>
                            @endif
                            @if($consultation->dossierMedical?->actif)
                                <a href="{{ route('professional.workspace.patient.dossier', $consultation->dossierMedical) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/20 border border-white/30 text-xs font-semibold hover:bg-white/30 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Dossier médical
                                </a>
                            @endif
                            @if($patientWhatsappUrl)
                                <a href="{{ $patientWhatsappUrl }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/80 border border-emerald-300/60 text-xs font-semibold hover:bg-emerald-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-13.773 7.68L3 21l1.32-4.227A9 9 0 1121 12z"/></svg>
                                    WhatsApp patient
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900/30">
                    @if($patientWhatsappUrl)
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ $patientWhatsappShareConsultationUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-gray-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-700 hover:bg-white transition-colors">Partager consultation</a>
                            <a href="{{ $patientWhatsappShareSummaryUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-gray-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-700 hover:bg-white transition-colors">Partager synthèse</a>
                            @if($patientWhatsappShareResultUrl)
                                <a href="{{ $patientWhatsappShareResultUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-gray-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-700 hover:bg-white transition-colors">Partager résultat</a>
                            @endif
                        </div>
                    @else
                        <p class="text-xs font-medium text-amber-700 dark:text-amber-300">Numéro patient indisponible: ajoute un numéro dans le profil patient pour activer le contact WhatsApp.</p>
                    @endif
                </div>

                {{-- Tab navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 px-4">
                    <nav class="-mb-px flex gap-0 overflow-x-auto" aria-label="Onglets consultation">
                        @foreach([
                            ['id' => 'consultation', 'label' => 'Consultation', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['id' => 'ordonnances', 'label' => 'Ordonnance', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                            ['id' => 'examens', 'label' => 'Examens', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                            ['id' => 'suivi', 'label' => 'Suivi', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                            ['id' => 'had', 'label' => 'HAD', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['id' => 'documents', 'label' => 'Documents', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ] as $tabItem)
                            <button type="button"
                                x-on:click="tab = '{{ $tabItem['id'] }}'"
                                :class="tab === '{{ $tabItem['id'] }}' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 bg-white dark:bg-gray-800' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300'"
                                class="flex items-center gap-1.5 whitespace-nowrap py-3 px-3 border-b-2 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $tabItem['icon'] }}"/></svg>
                                {{ $tabItem['label'] }}
                                @if($tabItem['id'] === 'documents' && $consultation->documents->isNotEmpty())
                                    <span class="ml-0.5 px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold">{{ $consultation->documents->count() }}</span>
                                @endif
                            </button>
                        @endforeach
                    </nav>
                </div>


                {{-- MAIN FORM (tabs 1 to 5)--}}

                <form method="POST" action="{{ route('professional.workspace.consultation.update', $consultation) }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="_active_tab" :value="tab">
                    <input type="hidden" name="type_consultation" value="{{ old('type_consultation', $consultation->type_consultation ?? ($mode === 'teleconsultation' ? 'visio_teleconsultation' : 'presentiel')) }}">

                    {{-- ===== TAB 1: CONSULTATION ===== --}}
                    <div x-show="tab === 'consultation'" class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Constantes cliniques</h3>
                            <a href="{{ route('professional.workspace.consultation.print', $consultation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-sm font-semibold hover:bg-sky-200 dark:hover:bg-sky-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimer consultation
                            </a>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Température (°C)</label>
                                <input type="number" step="0.1" name="temperature" value="{{ old('temperature', $consultation->temperature) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="37.5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tension (mmHg)</label>
                                <input type="text" name="tension_arterielle" value="{{ old('tension_arterielle', $consultation->tension_arterielle) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="120/80">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Glycémie (g/L)</label>
                                <input type="number" step="0.01" name="taux_glycemie" value="{{ old('taux_glycemie', $consultation->taux_glycemie) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="1.10">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poids (kg)</label>
                                <input type="number" step="0.1" name="poids" value="{{ old('poids', $consultation->poids) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="70.0">
                            </div>
                        </div>

                        @if($mode === 'teleconsultation')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lien de téléconsultation</label>
                                <input type="url" name="lien_teleconsultation" value="{{ old('lien_teleconsultation', $consultation->lien_teleconsultation) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="https://...">
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Symptômes rapportés</label>
                            <textarea name="symptomes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('symptomes', $consultation->symptomes) }}</textarea>
                        </div>

                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest pt-2">Évaluation clinique</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diagnostic médecin</label>
                                <textarea name="diagnostic_medecin" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('diagnostic_medecin', $consultation->diagnostic_medecin) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diagnostic complémentaire</label>
                                <textarea name="diagnostic" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('diagnostic', $consultation->diagnostic) }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conclusion</label>
                            <textarea name="conclusion" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('conclusion', $consultation->conclusion) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note résultat</label>
                                <textarea name="note_resultat" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('note_resultat', $consultation->note_resultat) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fichier résultat (upload)</label>
                                <input type="file" name="fichier_resultat" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 cursor-pointer">
                                @if($consultation->fichier_resultat_path)
                                    <a href="{{ Storage::url($consultation->fichier_resultat_path) }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-300 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir le fichier résultat actuel
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ===== TAB 2: ORDONNANCES ===== --}}
                    <div x-show="tab === 'ordonnances'" class="space-y-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Ordonnance médicale</h3>
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    formaction="{{ route('professional.workspace.consultation.treatment-suggestion', $consultation) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-sm font-semibold hover:bg-amber-200 dark:hover:bg-amber-900/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18l-1.813-2.096a5.5 5.5 0 01.492-7.188l.03-.029a5.5 5.5 0 117.778 7.778l-.03.03a5.5 5.5 0 01-5.644 1.409z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                                    Suggestion IA
                                </button>
                                <a href="{{ route('professional.workspace.consultation.ordonnance.print', $consultation) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-sm font-semibold hover:bg-emerald-200 dark:hover:bg-emerald-900/60 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Imprimer ordonnance / prescription
                                </a>
                            </div>
                        </div>

                        <div class="rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50/70 dark:bg-amber-900/10 p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Assistant IA à la demande</p>
                                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-400">Génère une proposition de traitement, de prescription et de suivi à partir des éléments déjà saisis. La validation finale reste médicale.</p>
                                </div>
                                @if(session('treatment_ai_suggestion_generated'))
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ session('treatment_ai_suggestion.source') === 'assistant_ia' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">{{ session('treatment_ai_suggestion.source') === 'assistant_ia' ? 'IA active' : 'Fallback local' }}</span>
                                @endif
                            </div>

                            @if(session('treatment_ai_suggestion_generated'))
                                @php($suggestion = session('treatment_ai_suggestion'))
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Résumé clinique</p>
                                            <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $suggestion['resume_clinique'] ?? 'Résumé indisponible.' }}</p>
                                        </div>

                                        @if(!empty($suggestion['ordonnance_proposee']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Ordonnance proposée</p>
                                                <ul class="mt-2 space-y-1 text-gray-700 dark:text-gray-200">
                                                    @foreach($suggestion['ordonnance_proposee'] as $ligne)
                                                        <li>• {{ $ligne }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        @if(!empty($suggestion['prescription_proposee']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Prescription proposée</p>
                                                <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $suggestion['prescription_proposee'] }}</p>
                                            </div>
                                        @endif

                                        @if(!empty($suggestion['recommandations_proposees']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Recommandations proposées</p>
                                                <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $suggestion['recommandations_proposees'] }}</p>
                                            </div>
                                        @endif

                                        @if(!empty($suggestion['suivi_propose']))
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-400">Suivi proposé</p>
                                                <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $suggestion['suivi_propose'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(!empty($suggestion['points_attention']))
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-widest text-rose-700 dark:text-rose-400">Points à vérifier avant validation</p>
                                        <ul class="mt-2 space-y-1 text-sm text-rose-800 dark:text-rose-200">
                                            @foreach($suggestion['points_attention'] as $point)
                                                <li>• {{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Médicaments prescrits <span class="text-gray-400 font-normal">(1 ligne = 1 médicament)</span></label>
                            <textarea name="ordonnance_produits" rows="7" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm font-mono" placeholder="Amoxicilline 500mg — 3x/j pendant 7 jours&#10;Paracétamol 500mg — si douleur, max 3x/j">{{ old('ordonnance_produits', $ordonnanceActive ? collect($ordonnanceActive->produits ?? [])->implode("\n") : '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prescription détaillée</label>
                                <textarea name="ordonnance_prescription" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('ordonnance_prescription', $ordonnanceActive?->prescription) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recommandations</label>
                                <textarea name="ordonnance_recommandations" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('ordonnance_recommandations', $ordonnanceActive?->recommandations) }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instructions complémentaires</label>
                            <textarea name="ordonnance_instructions" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('ordonnance_instructions', $ordonnanceActive?->instructions_complementaires) }}</textarea>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer select-none">
                            <input type="checkbox" name="imprimer_ordonnance" value="1" class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                            Ouvrir l'aperçu imprimable après enregistrement
                        </label>
                    </div>

                    {{-- ===== TAB 3: EXAMENS ===== --}}
                    <div x-show="tab === 'examens'" class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Prescription d'examen</h3>
                            <a href="{{ route('professional.workspace.consultation.summary.print', $consultation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-sm font-semibold hover:bg-violet-200 dark:hover:bg-violet-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimer rubrique examens
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mode de prescription</label>
                                <select name="examen_mode_orientation" x-model="orientationMode" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                    <option value="interne">Réalisé dans ma structure</option>
                                    <option value="recommandation">Recommandation vers un autre professionnel</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Libellé de l'examen</label>
                                <input type="text" name="examen_libelle" value="{{ old('examen_libelle') }}" :disabled="orientationMode === 'interne'" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm disabled:opacity-60 disabled:cursor-not-allowed" placeholder="Ex: Bilan sanguin complet (recommandation)">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">En mode interne, le nom de l'examen est pris depuis le service sélectionné.</p>
                            </div>
                        </div>

                        <template x-if="orientationMode === 'recommandation'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Examens à recommander <span class="text-gray-400 font-normal">(1 ligne = 1 examen)</span></label>
                                <textarea name="examen_libelles" rows="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Bilan sanguin complet&#10;ECBU&#10;Radiographie thorax">{{ old('examen_libelles') }}</textarea>
                            </div>
                        </template>

                        <template x-if="orientationMode === 'interne'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service interne à associer</label>
                                <select name="examen_service_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                    <option value="">Aucun service spécifique</option>
                                    @foreach($servicesExamenInternes as $serviceInterne)
                                        <option value="{{ $serviceInterne->id }}">{{ $serviceInterne->nom }} — {{ number_format((float) $serviceInterne->prix, 0, ',', ' ') }} XAF</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>

                        <template x-if="orientationMode === 'recommandation'">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rechercher un professionnel externe</label>
                                    <input type="text" x-model="searchExternal" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Spécialité, nom, ville...">
                                </div>

                                <div class="max-h-52 overflow-auto border border-gray-200 dark:border-gray-700 rounded-xl p-2 space-y-1 bg-gray-50 dark:bg-gray-900/30">
                                    <template x-for="pro in filteredProfessionals" :key="pro.id">
                                        <button type="button" x-on:click="selectProfessional(pro)"
                                            :class="selectedExternal && selectedExternal.id === pro.id ? 'bg-violet-100 dark:bg-violet-900/30 border-violet-300 dark:border-violet-600' : 'border-transparent hover:bg-violet-50 dark:hover:bg-violet-900/10 hover:border-violet-200 dark:hover:border-violet-700'"
                                            class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="pro.name"></p>
                                            <p class="text-xs text-gray-600 dark:text-gray-300" x-text="pro.specialite + ' · ' + (pro.city || 'Ville non renseignée')"></p>
                                        </button>
                                    </template>
                                    <p x-show="filteredProfessionals.length === 0" class="px-3 py-3 text-sm text-gray-400 text-center">Aucun résultat.</p>
                                </div>

                                <input type="hidden" name="examen_dossier_professionnel_cible_id" :value="selectedExternal ? selectedExternal.id : ''">

                                <div x-show="selectedExternal" class="rounded-xl border border-violet-200 dark:border-violet-700 p-4 bg-violet-50/50 dark:bg-violet-900/10">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Service chez
                                        <span class="text-violet-700 dark:text-violet-300 font-semibold" x-text="selectedExternal ? selectedExternal.name : ''"></span>
                                    </label>
                                    <select name="examen_service_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                        <option value="">Sélectionner un service</option>
                                        <template x-for="service in (selectedExternal ? selectedExternal.services : [])" :key="service.id">
                                            <option :value="service.id" x-text="service.nom + ' — ' + service.prix"></option>
                                        </template>
                                    </select>
                                    <p class="mt-1.5 text-xs text-violet-600 dark:text-violet-300">Commission de recommandation appliquée automatiquement : 10 %</p>
                                </div>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact WhatsApp</label>
                                <input type="text" name="examen_whatsapp" value="{{ old('examen_whatsapp') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="+237...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note d'orientation</label>
                                <input type="text" name="examen_note_orientation" value="{{ old('examen_note_orientation') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Contexte médical / urgence">
                            </div>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200 cursor-pointer select-none">
                            <input type="checkbox" name="creer_examen" value="1" class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500">
                            Créer la prescription d'examen à l'enregistrement
                        </label>

                        @if($consultation->examens->isNotEmpty())
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Examens déjà prescrits ({{ $consultation->examens->count() }})</p>
                                @foreach($consultation->examens as $examen)
                                    <div class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ $examen->libelle }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                Réalisateur: {{ $examen->dossierProfessionnel?->user?->name ?? '—' }}
                                                &nbsp;·&nbsp; Statut: <span class="capitalize">{{ $examen->statut }}</span>
                                                &nbsp;·&nbsp; Commission: {{ number_format((float) $examen->commission_recommandation_montant, 0, ',', ' ') }} XAF
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- ===== TAB 4: SUIVI PATIENT ===== --}}
                    <div x-show="tab === 'suivi'" class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Suivi patient</h3>
                            <a href="{{ route('professional.workspace.consultation.summary.print', $consultation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-sm font-semibold hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimer rubrique suivi
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observations de suivi</label>
                                <textarea name="observations" rows="5" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Etat clinique actuel, évolution, incidents...">{{ old('observations', $consultation->observations) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recommandations de suivi</label>
                                <textarea name="recommandations" rows="5" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Conduite à tenir, contrôles, consignes patient...">{{ old('recommandations', $consultation->recommandations) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-xl border border-dashed border-amber-300 dark:border-amber-700 p-6 bg-amber-50/30 dark:bg-amber-900/5 text-center">
                            <svg class="mx-auto w-10 h-10 text-amber-400 dark:text-amber-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">Module de suivi soignant</p>
                            <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">Les notes d'évolution et retours soignants seront disponibles ici prochainement.</p>
                        </div>
                    </div>

                    {{-- ===== TAB 5: HAD ===== --}}
                    <div x-show="tab === 'had'" class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Hospitalisation à domicile (HAD)</h3>
                            <a href="{{ route('professional.workspace.consultation.summary.print', $consultation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-sm font-semibold hover:bg-rose-200 dark:hover:bg-rose-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimer rubrique HAD
                            </a>
                        </div>

                        <div class="rounded-xl border border-dashed border-rose-300 dark:border-rose-700 p-10 bg-rose-50/30 dark:bg-rose-900/5 text-center">
                            <svg class="mx-auto w-12 h-12 text-rose-400 dark:text-rose-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <p class="text-base font-semibold text-rose-700 dark:text-rose-400">Module HAD en cours de développement</p>
                            <p class="text-sm text-rose-500 dark:text-rose-500 mt-1 max-w-md mx-auto">La gestion des soins à domicile, du planning infirmier et des prescriptions HAD sera disponible dans une prochaine mise à jour.</p>
                        </div>
                    </div>

                    {{-- Statut + Submit (tabs 1–5 only) --}}
                    <div x-show="tab !== 'documents'" class="pt-5 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            <select name="statut" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="brouillon" @selected(old('statut', $consultation->statut) === 'brouillon')>Brouillon</option>
                                <option value="finalise" @selected(old('statut', $consultation->statut) === 'finalise')>Finalisée</option>
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('professional.workspace.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Retour</a>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">Enregistrer</button>
                        </div>
                    </div>
                </form>

                {{-- ============================== --}}
                {{-- TAB 6: DOCUMENTS             --}}
                {{-- ============================== --}}
                <div x-show="tab === 'documents'" class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Échange de documents</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ $consultation->documents->count() }} fichier(s)</span>
                            <a href="{{ route('professional.workspace.consultation.summary.print', $consultation) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-semibold hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Imprimer synthèse complète
                            </a>
                        </div>
                    </div>

                    <form method="POST"
                        action="{{ route('professional.workspace.consultation.document.store', $consultation) }}"
                        enctype="multipart/form-data"
                        class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-5 bg-gray-50 dark:bg-gray-900/20 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ajouter un document</label>
                            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 cursor-pointer">
                            <p class="mt-1.5 text-xs text-gray-400">Formats acceptés : PDF, JPG, PNG, DOC, DOCX, XLS, XLSX · Taille max : 10 Mo</p>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Envoyer le fichier
                        </button>
                    </form>

                    @if($consultation->documents->isEmpty())
                        <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                            <svg class="mx-auto w-12 h-12 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm">Aucun document partagé pour cette consultation.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($consultation->documents->sortByDesc('created_at') as $document)
                                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                                        {{ str_contains($document->mime_type ?? '', 'pdf') ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : (str_contains($document->mime_type ?? '', 'image') ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') }}">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $document->nom_fichier }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            Par <span class="font-medium">{{ $document->uploadedBy?->name ?? '—' }}</span>
                                            &nbsp;·&nbsp;
                                            <span class="capitalize px-1.5 py-0.5 rounded text-xs font-medium {{ $document->source === 'patient' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' }}">{{ $document->source }}</span>
                                            &nbsp;·&nbsp;
                                            {{ number_format($document->taille_octets / 1024, 1) }} Ko
                                            &nbsp;·&nbsp;
                                            {{ $document->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                            class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Télécharger">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('professional.workspace.consultation.document.destroy', [$consultation, $document]) }}"
                                            x-data
                                            x-on:submit.prevent="if(confirm('Supprimer ce document définitivement ?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2 flex justify-start">
                        <a href="{{ route('professional.workspace.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Retour au tableau de bord</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

