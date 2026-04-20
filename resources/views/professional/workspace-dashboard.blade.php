<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Espace de travail professionnel</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestion des rendez-vous, factures et dossiers de consultation</p>
            @if(($backofficeFeedbackUnreadCount ?? 0) > 0)
                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700 text-xs font-medium">
                    <span class="inline-flex w-2 h-2 rounded-full bg-amber-500"></span>
                    Nouveau retour backoffice: {{ $backofficeFeedbackUnreadCount }} non lu(s)
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Rendez-vous en attente</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $rendezVousEnAttente->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Services actifs</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->servicesActifs->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Dossier professionnel</p>
                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->raison_sociale ?? 'Pratique individuelle' }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Spécialité: {{ $dossierProfessionnel->specialite ?? '—' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Backoffice en cours</p>
                    <p class="mt-2 text-xl font-bold text-amber-700 dark:text-amber-300">{{ number_format((float) $financeStats['encours_backoffice'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Factures payées</p>
                    <p class="mt-2 text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ number_format((float) $financeStats['factures_payees'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Patient en attente</p>
                    <p class="mt-2 text-xl font-bold text-orange-700 dark:text-orange-300">{{ number_format((float) $financeStats['attente_patient'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Commissions en attente</p>
                    <p class="mt-2 text-xl font-bold text-violet-700 dark:text-violet-300">{{ number_format((float) $financeStats['commission_en_attente'], 0, ',', ' ') }} XAF</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Commissions payées</p>
                    <p class="mt-2 text-xl font-bold text-cyan-700 dark:text-cyan-300">{{ number_format((float) $financeStats['commission_payee'], 0, ',', ' ') }} XAF</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-sky-50 dark:bg-sky-900/10">
                    <h3 class="text-sm font-semibold text-sky-700 dark:text-sky-300 uppercase tracking-wide">Navigation rapide (5 pages dediees)</h3>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                    <a href="{{ route('professional.workspace.presentiel') }}" class="rounded-xl border border-cyan-200 dark:border-cyan-700 bg-cyan-50/70 dark:bg-cyan-900/20 p-4 hover:shadow transition">
                        <p class="mt-1 text-sm font-semibold text-cyan-900 dark:text-cyan-100">Patient presentiel</p>
                        <p class="mt-2 text-xs text-cyan-700/80 dark:text-cyan-300/80">Carte/numero dossier, consultation editable, facture physique.</p>
                    </a>

                    <a href="{{ route('professional.workspace.patients.tracking') }}" class="rounded-xl border border-teal-200 dark:border-teal-700 bg-teal-50/70 dark:bg-teal-900/20 p-4 hover:shadow transition">
                        <p class="mt-1 text-sm font-semibold text-teal-900 dark:text-teal-100">Suivi patients</p>
                        <p class="mt-2 text-xs text-teal-700/80 dark:text-teal-300/80">Etat des patients suivis et acces direct au dossier.</p>
                    </a>

                    <a href="{{ route('professional.workspace.finance') }}" class="rounded-xl border border-amber-200 dark:border-amber-700 bg-amber-50/70 dark:bg-amber-900/20 p-4 hover:shadow transition">
                        <p class="mt-1 text-sm font-semibold text-amber-900 dark:text-amber-100">Transactions et retrait</p>
                        <p class="mt-2 text-xs text-amber-700/80 dark:text-amber-300/80">Suivi financier, paiement physique, demande retrait backoffice.</p>
                    </a>

                    <a href="{{ route('professional.workspace.patients.directory') }}" class="rounded-xl border border-indigo-200 dark:border-indigo-700 bg-indigo-50/70 dark:bg-indigo-900/20 p-4 hover:shadow transition">
                        <p class="mt-1 text-sm font-semibold text-indigo-900 dark:text-indigo-100">Repertoire patients</p>
                        <p class="mt-2 text-xs text-indigo-700/80 dark:text-indigo-300/80">Nom, prenom, telephone, genre, photo, acces rapide.</p>
                    </a>

                    <a href="{{ route('professional.workspace.patients.history') }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50/80 dark:bg-gray-900/40 p-4 hover:shadow transition">
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">Historique complet</p>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Historique complet patient avec acces consultation-edit.</p>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-cyan-50 dark:bg-cyan-900/10 flex items-center justify-between"
                     x-data="{ openServiceModal: false }">
                    <h3 class="text-sm font-semibold text-cyan-700 dark:text-cyan-300 uppercase tracking-wide">Mes services (table de prestation)</h3>
                    <button @click="openServiceModal = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un service
                    </button>

                    {{-- Modal création service --}}
                    <div x-show="openServiceModal" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         @keydown.escape.window="openServiceModal = false">
                        <div class="absolute inset-0 bg-black/50" @click="openServiceModal = false"></div>
                        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-4 flex items-center justify-between">
                                <h3 class="text-white font-semibold text-base">Nouveau Service</h3>
                                <button @click="openServiceModal = false" class="text-white/70 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('services-pro.store', $dossierProfessionnel) }}" class="p-6 space-y-4">
                                @csrf
                                <input type="hidden" name="_from" value="workspace">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du Service <span class="text-red-500">*</span></label>
                                    <input type="text" name="nom" value="{{ old('nom') }}" required
                                           placeholder="Ex: Consultation générale, Échographie..."
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                                    @error('nom')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-red-500">*</span></label>
                                        <select name="type" required
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="consultation" @selected(old('type') === 'consultation')>Consultation</option>
                                            <option value="examen" @selected(old('type') === 'examen')>Examen</option>
                                            <option value="hospitalisation" @selected(old('type') === 'hospitalisation')>Hospitalisation</option>
                                            <option value="chirurgie" @selected(old('type') === 'chirurgie')>Chirurgie</option>
                                            <option value="urgence" @selected(old('type') === 'urgence')>Urgence</option>
                                            <option value="autre" @selected(old('type') === 'autre')>Autre</option>
                                        </select>
                                        @error('type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (XAF) <span class="text-red-500">*</span></label>
                                        <input type="number" name="prix" value="{{ old('prix') }}" required min="0" step="100"
                                               placeholder="5000"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">
                                        @error('prix')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                    <textarea name="description" rows="2"
                                              placeholder="Détails du service (optionnel)"
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500">{{ old('description') }}</textarea>
                                </div>

                                <div class="flex justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" @click="openServiceModal = false"
                                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm transition">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium transition">
                                        Ajouter le service
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    @if($servicesActifs->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun service actif configuré.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">Service</th>
                                        <th class="py-2 pr-4">Type</th>
                                        <th class="py-2 pr-4">Prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servicesActifs as $service)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-3 pr-4 text-gray-900 dark:text-gray-100 font-medium">{{ $service->nom }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $service->type_label }}</td>
                                            <td class="py-3 pr-4 text-gray-700 dark:text-gray-200">{{ number_format((float) $service->prix, 0, ',', ' ') }} XAF</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-amber-50 dark:bg-amber-900/10">
                    <h3 class="text-sm font-semibold text-amber-700 dark:text-amber-300 uppercase tracking-wide">Rendez-vous patients à traiter</h3>
                </div>
                <div class="p-5">
                    @if($rendezVousEnAttente->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun rendez-vous en attente pour le moment.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($rendezVousEnAttente as $rdv)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        <div class="space-y-1 text-sm">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $rdv->patient->name ?? 'Patient' }} • {{ $rdv->reference }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Service: {{ $rdv->serviceProfessionnel?->nom ?? 'Service non défini' }} ({{ $rdv->serviceProfessionnel?->type_label ?? '—' }})</p>
                                            <p class="text-gray-600 dark:text-gray-300">Montant prévu: {{ number_format((float) ($rdv->serviceProfessionnel?->prix ?? 0), 0, ',', ' ') }} XAF</p>
                                            <p class="text-gray-600 dark:text-gray-300">Date RDV: {{ optional($rdv->date_proposee)->format('d/m/Y H:i') }}</p>
                                            @if($rdv->motif)
                                                <p class="text-gray-600 dark:text-gray-300">Motif: {{ $rdv->motif }}</p>
                                            @endif
                                        </div>

                                        <div class="flex flex-col gap-2 w-full lg:w-72">
                                            <form method="POST" action="{{ route('professional.workspace.rendez-vous.accept', $rdv) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                @if(($rdv->mode_deroulement ?? 'presentiel') === 'teleconsultation')
                                                    <input
                                                        type="url"
                                                        name="lien_teleconsultation_medecin"
                                                        required
                                                        placeholder="Lien visio médecin (https://...)"
                                                        class="w-full px-3 py-2 rounded-lg border border-blue-300 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-gray-100 text-xs"
                                                    >
                                                @endif
                                                <input type="text" name="notes_professionnel" placeholder="Note (optionnelle)" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-xs">
                                                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium">Accepter</button>
                                            </form>

                                            <form method="POST" action="{{ route('professional.workspace.rendez-vous.decline', $rdv) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="notes_professionnel" placeholder="Motif du refus" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-xs">
                                                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium">Décliner</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-violet-50 dark:bg-violet-900/10">
                    <h3 class="text-sm font-semibold text-violet-700 dark:text-violet-300 uppercase tracking-wide">Demandes d'examen recommandées à valider</h3>
                </div>
                <div class="p-5">
                    @if($examensEnAttenteValidation->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune demande d'examen en attente d'approbation.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($examensEnAttenteValidation as $examen)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        <div class="space-y-1 text-sm">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $examen->patient?->name ?? 'Patient' }} • {{ $examen->libelle }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Service: {{ $examen->serviceProfessionnel?->nom ?? 'Service indisponible' }}</p>
                                            <p class="text-gray-600 dark:text-gray-300">Référence dossier: {{ $examen->numero_dossier_reference ?? '—' }}</p>
                                            @if($examen->note_orientation)
                                                <p class="text-gray-600 dark:text-gray-300">Note orientation: {{ $examen->note_orientation }}</p>
                                            @endif
                                        </div>

                                        <div class="w-full lg:w-72">
                                            <form method="POST" action="{{ route('professional.workspace.examen.accept', $examen) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium">Accepter la demande et générer la facture</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Historique récent</h3>
                </div>
                <div class="p-5">
                    @if($rendezVousTraites->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun rendez-vous traité pour le moment.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">Patient</th>
                                        <th class="py-2 pr-4">Service</th>
                                        <th class="py-2 pr-4">Statut</th>
                                        <th class="py-2 pr-4">Facture</th>
                                        <th class="py-2 pr-4">Consultation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rendezVousTraites as $rdv)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-3 pr-4 text-gray-900 dark:text-gray-100">{{ $rdv->patient->name ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $rdv->serviceProfessionnel?->nom ?? '—' }}</td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $rdv->statut === 'accepte' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : ($rdv->statut === 'termine' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300') }}">
                                                    {{ ucfirst($rdv->statut) }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $rdv->facture?->reference ?? '—' }}</td>
                                            <td class="py-3 pr-4">
                                                @if($rdv->consultation)
                                                    <a href="{{ route('professional.workspace.consultation.edit', $rdv->consultation) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                        Remplir / Mettre à jour
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = @json((int) auth()->id());
            const dossierProfessionnelId = @json((int) ($dossierProfessionnel->id ?? 0));
            const reverbKey = @json(config('broadcasting.connections.reverb.key'));
            const reverbHost = @json(config('broadcasting.connections.reverb.options.host'));
            const reverbPort = Number(@json(config('broadcasting.connections.reverb.options.port')));
            const reverbScheme = @json(config('broadcasting.connections.reverb.options.scheme'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            if (!window.Pusher || !reverbKey || !reverbHost || !userId || dossierProfessionnelId <= 0) {
                return;
            }

            const toastContainer = document.createElement('div');
            toastContainer.className = 'fixed top-4 right-4 z-[70] space-y-2';
            document.body.appendChild(toastContainer);

            const showToast = (message, tone = 'info') => {
                const toneClasses = {
                    info: 'bg-cyan-600',
                    success: 'bg-emerald-600',
                    warning: 'bg-amber-600',
                };

                const toast = document.createElement('div');
                toast.className = `max-w-sm px-4 py-3 rounded-xl text-white shadow-xl ${toneClasses[tone] ?? toneClasses.info}`;
                toast.textContent = message;

                toastContainer.appendChild(toast);

                window.setTimeout(() => {
                    toast.remove();
                }, 5000);
            };

            const pusher = new window.Pusher(reverbKey, {
                wsHost: reverbHost,
                wsPort: reverbPort,
                wssPort: reverbPort,
                forceTLS: reverbScheme === 'https',
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                },
            });

            const proChannel = pusher.subscribe(`private-professionnel.${dossierProfessionnelId}`);
            proChannel.bind('rendez-vous.soumis', function (payload) {
                const patientName = payload?.patient ?? 'Patient';
                const ref = payload?.reference ? ` (${payload.reference})` : '';
                showToast(`Nouvelle demande de ${patientName}${ref}.`, 'info');
            });

            const userNotificationChannel = pusher.subscribe(`private-App.Models.User.${userId}`);
            userNotificationChannel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function (payload) {
                if (payload?.type === 'rendez_vous_soumis') {
                    showToast(payload?.message ?? 'Nouvelle demande de rendez-vous.', 'info');
                }
            });
        });
    </script>
@endpush
