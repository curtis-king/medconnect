<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">Tableau de bord patient</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Interface mobile-first pour vos paiements, depenses, rendez-vous, documents et alertes sante.</p>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-b from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 min-h-[70vh]">
        @php
            $professionalValidated = $myProfessionalDossier?->statut === 'valide';
            $professionalCardHref = $professionalValidated
                ? route('professional.workspace.dashboard')
                : ($myProfessionalDossier ? route('user.professional.profile') : route('user.professional.create'));
            $pendingMedicalRenewalReferences = collect(session('pending_medical_renewal_references', $pendingMedicalRenewalReferences ?? []))
                ->filter()
                ->values()
                ->all();

            if (empty($pendingMedicalRenewalReferences) && session()->has('pending_medical_renewal_reference')) {
                $pendingMedicalRenewalReferences = [(string) session('pending_medical_renewal_reference')];
            }

            if (empty($pendingMedicalRenewalReferences) && !empty($pendingMedicalRenewalPayment?->reference_paiement)) {
                $pendingMedicalRenewalReferences = [(string) $pendingMedicalRenewalPayment->reference_paiement];
            }
        @endphp

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                    <p class="font-medium">Informations de paiement invalides.</p>
                    <ul class="mt-2 text-sm list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($pendingMedicalRenewalReferences))
                <div
                    id="pending-mobile-money-verification"
                    data-references='@json($pendingMedicalRenewalReferences)'
                    data-status-url="{{ route('patient.subscriptions.renew-medical.status') }}"
                    class="p-4 rounded-xl bg-blue-100 text-blue-900 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold">Verification Mobile Money en cours</p>
                            <p class="mt-1 text-sm" id="pending-mobile-money-message">
                                Nous attendons la reponse API operateur pour confirmer vos paiement(s). References: {{ implode(', ', $pendingMedicalRenewalReferences) }}
                            </p>
                        </div>
                        <div class="h-6 w-6 rounded-full border-2 border-blue-300 border-t-blue-700 animate-spin"></div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="rounded-2xl border border-rose-200 dark:border-rose-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <p class="text-xs text-rose-700 dark:text-rose-300 uppercase tracking-wide">Paiements attente</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingPaymentsCount }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 uppercase tracking-wide">Depenses payees</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format((float) $totalPaidExpenses, 0, ',', ' ') }} XAF</p>
                </div>
                <div class="rounded-2xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <p class="text-xs text-violet-700 dark:text-violet-300 uppercase tracking-wide">Rendez-vous</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $upcomingAppointmentsCount }}</p>
                </div>
                <div class="rounded-2xl border border-cyan-200 dark:border-cyan-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <p class="text-xs text-cyan-700 dark:text-cyan-300 uppercase tracking-wide">Documents</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $documentsCount }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-800 p-4 shadow-sm col-span-2 md:col-span-1">
                    <p class="text-xs text-amber-700 dark:text-amber-300 uppercase tracking-wide">Notifications</p>
                    <p data-unread-counter class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $unreadNotificationsCount }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Rubriques du tableau de bord</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <a href="#rubrique-profil" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">Profil</a>
                        <a href="#rubrique-finances" class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Finances</a>
                        <a href="#rubrique-soins" class="px-3 py-1.5 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">Soins & documents</a>
                    </div>
                </div>
            </div>

            <section id="rubrique-profil" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-gray-800 p-5 shadow-sm space-y-4">
                <div>
                    <p class="text-base font-semibold text-slate-900 dark:text-slate-100">Rubrique Profil</p>
                    <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Gestion des profils et des dossiers rattaches (vous, enfant/personne a charge).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ $myMedicalDossier ? route('user.medical-profile.edit') : route('user.adherer.create') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Mon profil medical</p>
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">Mettre a jour mon dossier et mes informations de sante.</p>
                    </a>
                    <a href="{{ $professionalCardHref }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Espace professionnel</p>
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">Suivre la validation pro ou acceder a l'espace de travail.</p>
                    </a>
                </div>

                <a href="{{ route('user.validation.status') }}" class="block rounded-2xl border border-orange-200 dark:border-orange-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                    <p class="text-sm font-semibold text-orange-900 dark:text-orange-100">Validation de mes profils</p>
                    <p class="mt-2 text-xs text-orange-700/90 dark:text-orange-300/90">Consulter les profils en attente (patient/professionnel). Reponse d'activation envoyee par mail.</p>
                </a>

                <div class="rounded-2xl border border-teal-200 dark:border-teal-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-teal-900 dark:text-teal-100">Declaration enfant / personne a charge</p>
                            <p class="mt-1 text-xs text-teal-700/90 dark:text-teal-300/90">Ajoutez un dossier medical a charge depuis cette rubrique profil.</p>
                        </div>
                        <a href="{{ route('user.adherer.create', ['mode' => 'dependant']) }}" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2">
                            Declarer un dossier a charge
                        </a>
                    </div>
                </div>
            </section>

            <section id="rubrique-finances" class="space-y-4">
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-base font-semibold text-emerald-900 dark:text-emerald-100">Rubrique Finances</p>
                    <p class="mt-1 text-xs text-emerald-700/90 dark:text-emerald-300/90">Tout ce qui concerne paiements, depenses et reabonnements est regroupe ici.</p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('patient.payments.index') }}" class="rounded-2xl border border-rose-200 dark:border-rose-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                            <p class="text-sm font-semibold text-rose-900 dark:text-rose-100">Mes paiements</p>
                            <p class="mt-2 text-xs text-rose-700/90 dark:text-rose-300/90">Payer personnellement ou soumettre au backoffice.</p>
                        </a>

                        <a href="{{ route('patient.finances.index') }}" class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                            <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">Finances</p>
                            <p class="mt-2 text-xs text-emerald-700/90 dark:text-emerald-300/90">Suivre vos depenses et leur evolution.</p>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-purple-200 dark:border-purple-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-purple-900 dark:text-purple-100">Sommation estimative de la charge mensuelle</p>
                            <p class="mt-1 text-xs text-purple-700/90 dark:text-purple-300/90">
                                Estimation basee sur les frais de reabonnement (dossiers rattaches + option professionnelle).
                            </p>
                        </div>
                        <p class="text-xl font-bold text-purple-900 dark:text-purple-100">{{ number_format((float) $estimatedTotalMonthlyCharge, 0, ',', ' ') }} XAF / mois</p>
                    </div>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-slate-700 dark:text-slate-300">
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                            Patient: {{ number_format((float) $estimatedMedicalMonthlyCharge, 0, ',', ' ') }} XAF
                            ({{ ($managedMedicalDossiers ?? collect())->count() }} dossier(s) x {{ number_format((float) $reabonnementUnitPatient, 0, ',', ' ') }} XAF)
                        </div>
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                            Professionnel: {{ number_format((float) $estimatedProfessionalMonthlyCharge, 0, ',', ' ') }} XAF
                            @if($myProfessionalDossier)
                                (1 x {{ number_format((float) $reabonnementUnitProfessional, 0, ',', ' ') }} XAF)
                            @else
                                (non active)
                            @endif
                        </div>
                        <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-3 font-semibold">
                            Total estime: {{ number_format((float) $estimatedTotalMonthlyCharge, 0, ',', ' ') }} XAF
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-cyan-200 dark:border-cyan-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold text-cyan-900 dark:text-cyan-100">Paiement en ligne des dossiers rattaches</p>
                        <p class="mt-1 text-xs text-cyan-700/90 dark:text-cyan-300/90">Meme procedure pour tous les dossiers: selection du dossier et paiement en ligne (Mobile Money / Carte).</p>
                    </div>

                    <div class="mt-4 space-y-2">
                        @forelse(($managedMedicalDossiers ?? collect()) as $managedDossier)
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $managedDossier->nom_complet }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $managedDossier->numero_unique }}
                                        @if($managedDossier->est_personne_a_charge)
                                            · {{ $managedDossier->lien_avec_responsable_label ?? ucfirst((string) $managedDossier->lien_avec_responsable) }}
                                        @else
                                            · Dossier personnel
                                        @endif
                                        · Inscription: {{ ucfirst((string) ($managedDossier->statut_paiement_inscription ?? 'en_attente')) }}
                                    </p>
                                </div>
                                <a href="{{ route('user.adherer.payment', $managedDossier) }}" class="px-3 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium">Payer en ligne</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucun dossier medical rattache pour le moment.</p>
                        @endforelse
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-indigo-200 dark:border-indigo-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-100">Abonnement patient</p>
                        @if($medicalSubscription)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                Inactif
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                        @if($medicalSubscription)
                            Echeance: {{ $medicalSubscription->date_fin?->format('d/m/Y') }} ({{ $medicalSubscriptionDaysRemaining }} jour(s) restant(s))
                        @else
                            Aucun abonnement actif.
                        @endif
                    </p>

                    @if($myMedicalDossier)
                        <form method="POST" action="{{ route('patient.subscriptions.renew-medical') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2 js-renew-form" data-subscription-form="medical">
                            @csrf
                            <select name="dossier_reference" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2 md:col-span-3">
                                @foreach(($managedMedicalDossiers ?? collect([$myMedicalDossier])) as $managedDossier)
                                    <option value="{{ $managedDossier->numero_unique }}">
                                        {{ $managedDossier->nom_complet }} - {{ $managedDossier->numero_unique }}@if($managedDossier->est_personne_a_charge) ({{ $managedDossier->lien_avec_responsable_label ?? ucfirst((string) $managedDossier->lien_avec_responsable) }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <select name="nombre_mois" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                                <option value="1">1 mois</option>
                                <option value="3">3 mois</option>
                                <option value="6">6 mois</option>
                                <option value="12">12 mois</option>
                            </select>
                            <select name="mode_paiement" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2 js-payment-mode">
                                <option value="mobile_money">Mobile Money</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                            </select>
                            <button type="submit" class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-3 py-2">Payer reabonnement</button>

                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-2 js-mobile-money-fields">
                                <select name="mobile_money_provider" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                                    <option value="">Operateur</option>
                                    <option value="mtn">MTN Mobile Money</option>
                                    <option value="airtel">Airtel Money</option>
                                </select>
                                <input
                                    name="mobile_money_number"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Numero Mobile Money"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                            </div>

                            <div class="md:col-span-3 hidden space-y-2 js-card-fields">
                                <input
                                    name="card_holder_name"
                                    type="text"
                                    placeholder="Nom du titulaire"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                                <input
                                    name="stripe_payment_method_id"
                                    type="text"
                                    placeholder="Identifiant moyen de paiement Stripe (optionnel)"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                            </div>

                            <p class="md:col-span-3 text-xs text-slate-500 dark:text-slate-400">
                                Choisissez le dossier (vous, enfant ou personne a charge), puis payez par Mobile Money ou carte.
                            </p>
                        </form>
                    @endif
                </div>

                <div class="rounded-2xl border border-fuchsia-200 dark:border-fuchsia-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-fuchsia-900 dark:text-fuchsia-100">Abonnement professionnel</p>
                        @if($professionalSubscription)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                Inactif
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                        @if($myProfessionalDossier)
                            Statut dossier: {{ $myProfessionalDossier->statut_label ?? ucfirst((string) $myProfessionalDossier->statut) }}
                            @if($professionalSubscription)
                                | Echeance: {{ $professionalSubscription->date_fin?->format('d/m/Y') }} ({{ $professionalSubscriptionDaysRemaining }} jour(s) restant(s))
                            @endif
                        @else
                            Aucun dossier professionnel.
                        @endif
                    </p>

                    @if($myProfessionalDossier)
                        <form method="POST" action="{{ route('patient.subscriptions.renew-professional') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2 js-renew-form" data-subscription-form="professional">
                            @csrf
                            <select name="nombre_mois" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                                <option value="1">1 mois</option>
                                <option value="3">3 mois</option>
                                <option value="6">6 mois</option>
                                <option value="12">12 mois</option>
                            </select>
                            <select name="mode_paiement" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2 js-payment-mode">
                                <option value="mobile_money">Mobile Money</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                            </select>
                            <button type="submit" class="rounded-lg bg-fuchsia-600 hover:bg-fuchsia-700 text-white text-sm font-medium px-3 py-2">Payer reabonnement</button>

                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-2 js-mobile-money-fields">
                                <select name="mobile_money_provider" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                                    <option value="">Operateur</option>
                                    <option value="mtn">MTN Mobile Money</option>
                                    <option value="airtel">Airtel Money</option>
                                </select>
                                <input
                                    name="mobile_money_number"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Numero Mobile Money"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                            </div>

                            <div class="md:col-span-3 hidden space-y-2 js-card-fields">
                                <input
                                    name="card_holder_name"
                                    type="text"
                                    placeholder="Nom du titulaire"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                                <input
                                    name="stripe_payment_method_id"
                                    type="text"
                                    placeholder="Identifiant moyen de paiement Stripe (optionnel)"
                                    class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                                >
                            </div>

                            <p class="md:col-span-3 text-xs text-slate-500 dark:text-slate-400">
                                Mobile Money: simulation en attente d'API MTN/Airtel. Carte bancaire: transition vers Stripe.
                            </p>
                        </form>
                    @endif
                </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">Tout solder les reabonnements</p>
                        <p class="mt-1 text-xs text-emerald-700/90 dark:text-emerald-300/90">
                            Reglez en une seule operation tous les dossiers medicaux rattaches (option pro incluse).
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('patient.subscriptions.renew-all') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-2 js-renew-form" data-subscription-form="all">
                    @csrf
                    <select name="nombre_mois" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                        <option value="1">1 mois</option>
                        <option value="3">3 mois</option>
                        <option value="6">6 mois</option>
                        <option value="12">12 mois</option>
                    </select>
                    <select name="mode_paiement" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2 js-payment-mode">
                        <option value="mobile_money">Mobile Money</option>
                        <option value="carte_bancaire">Carte bancaire</option>
                    </select>
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
                        <input type="checkbox" name="include_professional" value="1" class="rounded border-slate-300 text-emerald-600">
                        Inclure abonnement professionnel
                    </label>
                    <button type="submit" class="rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-3 py-2">Tout solder</button>

                    <div class="md:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-2 js-mobile-money-fields">
                        <select name="mobile_money_provider" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2">
                            <option value="">Operateur</option>
                            <option value="mtn">MTN Mobile Money</option>
                            <option value="airtel">Airtel Money</option>
                        </select>
                        <input
                            name="mobile_money_number"
                            type="text"
                            inputmode="numeric"
                            placeholder="Numero Mobile Money"
                            class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                        >
                    </div>

                    <div class="md:col-span-4 hidden space-y-2 js-card-fields">
                        <input
                            name="card_holder_name"
                            type="text"
                            placeholder="Nom du titulaire"
                            class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                        >
                        <input
                            name="stripe_payment_method_id"
                            type="text"
                            placeholder="Identifiant moyen de paiement Stripe (optionnel)"
                            class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-gray-900 dark:text-gray-100 text-sm px-3 py-2"
                        >
                    </div>
                </form>
            </div>
            </section>

            <section id="rubrique-soins" class="rounded-2xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-gray-800 p-5 shadow-sm space-y-4">
                <div>
                    <p class="text-base font-semibold text-violet-900 dark:text-violet-100">Rubrique Soins, Rendez-vous et Documents</p>
                    <p class="mt-1 text-xs text-violet-700/90 dark:text-violet-300/90">Acces rapide au suivi medical, calendrier, documents et notifications.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <a href="{{ route('patient.appointments.index') }}" class="rounded-2xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <p class="text-sm font-semibold text-violet-900 dark:text-violet-100">Rendez-vous calendrier</p>
                        <p class="mt-2 text-xs text-violet-700/90 dark:text-violet-300/90">Visualiser tous les rendez-vous patient.</p>
                    </a>

                    <a href="{{ route('rendez-vous.index') }}" class="rounded-2xl border border-blue-200 dark:border-blue-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Prendre un rendez-vous</p>
                        <p class="mt-2 text-xs text-blue-700/90 dark:text-blue-300/90">Nouvelle demande de rendez-vous en quelques clics.</p>
                    </a>

                    <a href="{{ route('patient.documents.index') }}" class="rounded-2xl border border-cyan-200 dark:border-cyan-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <p class="text-sm font-semibold text-cyan-900 dark:text-cyan-100">Mes documents</p>
                        <p class="mt-2 text-xs text-cyan-700/90 dark:text-cyan-300/90">Resultats, ordonnances et fichiers medicaux recus.</p>
                    </a>

                    <a href="{{ route('patient.alerts.index') }}" class="rounded-2xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Alertes & notifications</p>
                            @if($unreadNotificationsCount > 0)
                                <span data-unread-indicator class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            @else
                                <span data-unread-indicator hidden class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-amber-700/90 dark:text-amber-300/90">Consultation, vaccins, traitements, ordonnances, resultats.</p>
                    </a>
                </div>
            </section>
        </div>

        <div id="payment-processing-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 border border-slate-200 dark:border-slate-700 shadow-xl p-6">
                <p class="text-base font-semibold text-slate-900 dark:text-slate-100">Traitement de la transaction...</p>
                <div class="mt-5 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    <div id="payment-step-validation" class="opacity-100">1. Validation des informations</div>
                    <div id="payment-step-verification" class="opacity-40">2. Verification operateur / carte</div>
                    <div id="payment-step-processing" class="opacity-40">3. Traitement en cours</div>
                </div>
                <div class="mt-5 h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                    <div id="payment-progress-bar" class="h-full w-0 bg-indigo-600 transition-all duration-700"></div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const forms = document.querySelectorAll('.js-renew-form');
                const overlay = document.getElementById('payment-processing-overlay');
                const stepValidation = document.getElementById('payment-step-validation');
                const stepVerification = document.getElementById('payment-step-verification');
                const stepProcessing = document.getElementById('payment-step-processing');
                const progressBar = document.getElementById('payment-progress-bar');
                const pendingVerificationBox = document.getElementById('pending-mobile-money-verification');

                const updateModeSections = function (form) {
                    const modeInput = form.querySelector('.js-payment-mode');
                    const mobileFields = form.querySelector('.js-mobile-money-fields');
                    const cardFields = form.querySelector('.js-card-fields');
                    const isMobile = modeInput && modeInput.value === 'mobile_money';

                    if (mobileFields) {
                        mobileFields.classList.toggle('hidden', !isMobile);
                    }

                    if (cardFields) {
                        cardFields.classList.toggle('hidden', isMobile);
                    }
                };

                forms.forEach(function (form) {
                    const modeInput = form.querySelector('.js-payment-mode');

                    if (modeInput) {
                        modeInput.addEventListener('change', function () {
                            updateModeSections(form);
                        });
                    }

                    updateModeSections(form);

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        if (!overlay || !stepValidation || !stepVerification || !stepProcessing || !progressBar) {
                            form.submit();
                            return;
                        }

                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                        stepValidation.classList.remove('opacity-40');
                        stepVerification.classList.add('opacity-40');
                        stepProcessing.classList.add('opacity-40');
                        progressBar.style.width = '30%';

                        setTimeout(function () {
                            stepVerification.classList.remove('opacity-40');
                            progressBar.style.width = '65%';
                        }, 500);

                        setTimeout(function () {
                            stepProcessing.classList.remove('opacity-40');
                            progressBar.style.width = '100%';
                        }, 1200);

                        setTimeout(function () {
                            form.submit();
                        }, 1900);
                    });
                });

                if (pendingVerificationBox) {
                    const statusUrl = pendingVerificationBox.getAttribute('data-status-url');
                    const referencesRaw = pendingVerificationBox.getAttribute('data-references');
                    const messageNode = document.getElementById('pending-mobile-money-message');
                    let references = [];

                    try {
                        references = JSON.parse(referencesRaw || '[]');
                    } catch (error) {
                        references = [];
                    }

                    references = Array.isArray(references)
                        ? references.filter(function (value) {
                            return !!value;
                        })
                        : [];

                    const updatePendingMessage = function (text) {
                        if (messageNode) {
                            messageNode.textContent = text;
                        }
                    };

                    const checkOneReference = function (reference) {
                        if (!statusUrl || !reference) {
                            return;
                        }

                        const url = new URL(statusUrl, window.location.origin);
                        url.searchParams.set('reference', reference);

                        fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                        })
                            .then(function (response) {
                                return response.json();
                            })
                            .then(function (payload) {
                                const status = payload && payload.status ? payload.status : 'pending';
                                const message = payload && payload.message
                                    ? payload.message
                                    : 'En attente de reponse API Mobile Money...';

                                updatePendingMessage(message + ' Reference: ' + reference);

                                if (status === 'paid' || status === 'failed') {
                                    window.location.reload();
                                }
                            })
                            .catch(function () {
                                updatePendingMessage('Verification API momentanement indisponible. Nouvelle tentative automatique... Reference: ' + reference);
                            });
                    };

                    const checkStatus = function () {
                        if (references.length === 0) {
                            return;
                        }

                        references.forEach(function (reference) {
                            checkOneReference(reference);
                        });
                    };

                    checkStatus();
                    setInterval(checkStatus, 4000);
                }
            });
        </script>
    </div>
</x-app-layout>
