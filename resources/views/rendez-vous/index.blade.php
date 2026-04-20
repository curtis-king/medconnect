<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Prendre un rendez-vous
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Trouvez rapidement un professionnel de la mutuelle et envoyez votre demande en temps réel.
            </p>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-cyan-50 via-white to-emerald-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 min-h-[70vh]"
         x-data="{ modalOpen: false, selectedPro: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    <p class="font-semibold">Veuillez corriger les erreurs:</p>
                    <ul class="list-disc ml-5 mt-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <form method="GET" action="{{ route('rendez-vous.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Recherche rapide</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nom ou prénom du professionnel"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-cyan-500 focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Spécialité</label>
                        <select name="specialite" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">Toutes</option>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite }}" @selected(request('specialite') === $specialite)>{{ $specialite }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Département</label>
                        <select name="ville" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">Tous</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville }}" @selected(request('ville') === $ville)>{{ $ville }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Arrondissement</label>
                        <select name="quartier" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="">Tous</option>
                            @foreach($quartiers as $quartier)
                                <option value="{{ $quartier }}" @selected(request('quartier') === $quartier)>{{ $quartier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-5 flex gap-2 justify-end">
                        <a href="{{ route('rendez-vous.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Réinitialiser</a>
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm bg-cyan-600 hover:bg-cyan-700 text-white">Rechercher</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse($professionnels as $pro)
                    @php
                        $proUser = $pro->user;
                        $services = $pro->servicesActifs;
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $proUser?->name ?? 'Professionnel' }}</h3>
                                    <p class="text-sm text-cyan-700 dark:text-cyan-300 font-medium">{{ $pro->specialite ?? 'Spécialité non renseignée' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                                    {{ $pro->type_structure_label }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                <p><span class="font-semibold">Adresse:</span> {{ $proUser?->getFullLocation() ?? 'Non renseignée' }}</p>
                                <p><span class="font-semibold">Téléphone:</span> {{ $proUser?->phone ?? 'Non renseigné' }}</p>
                                <p><span class="font-semibold">Services actifs:</span> {{ $services->count() }}</p>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button type="button"
                                        x-on:click="selectedPro = {
                                            id: {{ $pro->id }},
                                            nom: @js($proUser?->name),
                                            specialite: @js($pro->specialite),
                                            telephone: @js($proUser?->phone),
                                            services: @js($services->map(fn($service) => [
                                                'id' => $service->id,
                                                'nom' => $service->nom,
                                                'type' => $service->type_label,
                                                'prix' => number_format((float) $service->prix, 0, ',', ' ') . ' XAF',
                                            ])->values()),
                                        }; modalOpen = true;"
                                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-emerald-500 hover:from-cyan-700 hover:to-emerald-600 text-white text-sm font-semibold shadow-lg shadow-cyan-600/20">
                                    Contacter
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-2 p-8 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-300">Aucun professionnel ne correspond à vos critères.</p>
                    </div>
                @endforelse
            </div>

            <div>
                {{ $professionnels->links() }}
            </div>
        </div>

        <div x-show="modalOpen"
             x-transition
             class="fixed inset-0 z-50 flex items-center justify-center px-4"
             style="display: none;">
            <div class="absolute inset-0 bg-black/50" x-on:click="modalOpen = false"></div>

            <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-[90vh] overflow-auto">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Demande de rendez-vous</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedPro?.nom"></p>
                    </div>
                    <button type="button" x-on:click="modalOpen = false" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200">✕</button>
                </div>

                <form method="POST" action="{{ route('rendez-vous.store') }}" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="dossier_professionnel_id" :value="selectedPro?.id">

                    <div class="rounded-xl border border-teal-200 dark:border-teal-700 bg-teal-50 dark:bg-teal-900/20 p-4 space-y-3">
                        <p class="text-sm font-semibold text-teal-800 dark:text-teal-300">Beneficiaire (vous ou personne a charge)</p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Choisir un dossier medical rattache</label>
                            <select name="dossier_medical_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="">Dossier par defaut du compte</option>
                                @foreach($managedMedicalDossiers as $managedDossier)
                                    <option value="{{ $managedDossier->id }}" @selected((string) old('dossier_medical_id') === (string) $managedDossier->id)>
                                        {{ $managedDossier->nom_complet }} - {{ $managedDossier->numero_unique }}@if($managedDossier->est_personne_a_charge) ({{ $managedDossier->lien_avec_responsable_label ?? ucfirst((string) $managedDossier->lien_avec_responsable) }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Ou saisir un numero dossier</label>
                            <input
                                type="text"
                                name="patient_dossier_reference"
                                value="{{ old('patient_dossier_reference') }}"
                                placeholder="Ex: DM-2026-0004"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le numero saisi doit appartenir a un dossier medical rattache a votre compte.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Services et tarifs</label>
                        <template x-if="selectedPro && selectedPro.services && selectedPro.services.length > 0">
                            <div class="space-y-2">
                                <template x-for="service in selectedPro.services" :key="service.id">
                                    <label class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 cursor-pointer">
                                        <span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100" x-text="service.nom"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="' • ' + service.type"></span>
                                        </span>
                                        <span class="flex items-center gap-3">
                                            <span class="text-sm font-semibold text-cyan-700 dark:text-cyan-300" x-text="service.prix"></span>
                                            <input type="radio" name="service_professionnel_id" :value="service.id" class="text-cyan-600 focus:ring-cyan-500">
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </template>
                        <template x-if="!selectedPro || !selectedPro.services || selectedPro.services.length === 0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun service actif disponible pour ce professionnel.</p>
                        </template>
                    </div>

                    <div x-data="{ modeDeroulement: 'presentiel' }" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Mode de déroulement</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_deroulement" value="presentiel" x-model="modeDeroulement" class="text-indigo-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        <svg class="inline w-4 h-4 mr-1 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Présentiel
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_deroulement" value="teleconsultation" x-model="modeDeroulement" class="text-indigo-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        <svg class="inline w-4 h-4 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Téléconsultation
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div x-show="modeDeroulement === 'teleconsultation'" x-transition class="rounded-lg border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 p-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                En téléconsultation, le lien de connexion sera envoyé par le médecin après acceptation du rendez-vous.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Date</label>
                                <input type="date" name="date_proposee_jour" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Heure</label>
                                <input type="time" name="heure_proposee" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Motif</label>
                        <textarea name="motif" rows="3" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Décrivez brièvement votre besoin"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="modalOpen = false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200">Annuler</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-emerald-500 hover:from-cyan-700 hover:to-emerald-600 text-white font-semibold">
                            Envoyer la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = @json((int) auth()->id());
            const userRole = @json((string) auth()->user()->role);
            const dossierProfessionnelId = @json((int) (auth()->user()?->dossierProfessionnel?->id ?? 0));
            const reverbKey = @json(config('broadcasting.connections.reverb.key'));
            const reverbHost = @json(config('broadcasting.connections.reverb.options.host'));
            const reverbPort = Number(@json(config('broadcasting.connections.reverb.options.port')));
            const reverbScheme = @json(config('broadcasting.connections.reverb.options.scheme'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            if (!window.Pusher || !reverbKey || !reverbHost || !userId) {
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

            const patientChannel = pusher.subscribe(`private-patient.${userId}`);
            patientChannel.bind('rendez-vous.soumis', function (payload) {
                const text = payload?.reference
                    ? `Demande ${payload.reference} envoyee avec succes.`
                    : 'Votre demande de rendez-vous a ete envoyee.';
                showToast(text, 'success');
            });

            const userNotificationChannel = pusher.subscribe(`private-App.Models.User.${userId}`);
            userNotificationChannel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function (payload) {
                if (payload?.type === 'rendez_vous_soumis') {
                    showToast(payload?.message ?? 'Nouvelle mise a jour rendez-vous.', 'info');
                }
                if (payload?.type === 'rendez_vous_accepte') {
                    showToast(payload?.message ?? 'Votre rendez-vous a ete accepte.', 'success');
                }
                if (payload?.type === 'rendez_vous_decline') {
                    showToast(payload?.message ?? 'Votre rendez-vous a ete decline.', 'warning');
                }
            });

            if ((userRole === 'professional' || userRole === 'soignant') && dossierProfessionnelId > 0) {
                const proChannel = pusher.subscribe(`private-professionnel.${dossierProfessionnelId}`);
                proChannel.bind('rendez-vous.soumis', function (payload) {
                    const patientName = payload?.patient ?? 'Patient';
                    const ref = payload?.reference ? ` (${payload.reference})` : '';
                    showToast(`Nouvelle demande de ${patientName}${ref}.`, 'info');
                });
            }
        });
    </script>
@endpush
