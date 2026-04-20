<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mes paiements en attente</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Payer personnellement ou soumettre au backoffice avec notification automatique.</p>
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
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <span class="text-xs text-gray-500 dark:text-gray-400">Abonnement actif: {{ $activeSubscription ? 'Oui' : 'Non' }}</span>
            </div>

            <div class="space-y-4">
                @forelse($facturesEnAttente as $facture)
                    <div class="rounded-2xl border border-rose-200 dark:border-rose-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        @php
                            $latestSubmission = $facture->soumissionsMutuelle->sortByDesc('id')->first();
                            $isPendingSubmission = $latestSubmission && in_array($latestSubmission->statut, ['soumis', 'en_traitement'], true);
                            $isValidatedOrPaid = ($latestSubmission && in_array($latestSubmission->statut, ['approuve', 'partiel'], true)) || in_array($facture->statut_backoffice, ['valide', 'paye'], true);
                        @endphp

                        <div class="flex flex-col gap-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $facture->reference }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Service: {{ $facture->serviceProfessionnel?->nom ?? ucfirst((string) $facture->type_facture) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Montant: {{ number_format((float) $facture->montant_total, 0, ',', ' ') }} XAF</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @if(! $isValidatedOrPaid)
                                    <form method="POST" action="{{ route('patient.payments.pay', $facture) }}" class="rounded-xl border border-emerald-200 dark:border-emerald-700 p-3 bg-emerald-50/50 dark:bg-emerald-900/10 js-personal-payment-form">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-xs font-medium text-emerald-800 dark:text-emerald-200 mb-1">Payer personnellement</label>
                                        <select name="mode_paiement" class="w-full px-3 py-2 rounded-lg border border-emerald-300 dark:border-emerald-700 dark:bg-gray-900 dark:text-gray-100 text-sm js-personal-payment-mode">
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="carte">Carte bancaire</option>
                                        </select>

                                        <div class="mt-2 grid grid-cols-1 gap-2 js-personal-mobile-money-fields">
                                            <select name="mobile_money_provider" class="w-full px-3 py-2 rounded-lg border border-emerald-300 dark:border-emerald-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                                <option value="">Operateur</option>
                                                <option value="mtn">MTN Mobile Money</option>
                                                <option value="airtel">Airtel Money</option>
                                            </select>
                                            <input name="mobile_money_number" type="text" inputmode="numeric" placeholder="Numero Mobile Money" class="w-full px-3 py-2 rounded-lg border border-emerald-300 dark:border-emerald-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                        </div>

                                        <div class="mt-2 hidden space-y-2 js-personal-card-fields">
                                            <input name="card_holder_name" type="text" placeholder="Nom du titulaire" class="w-full px-3 py-2 rounded-lg border border-emerald-300 dark:border-emerald-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                            <input name="stripe_payment_method_id" type="text" placeholder="Identifiant Stripe (optionnel)" class="w-full px-3 py-2 rounded-lg border border-emerald-300 dark:border-emerald-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                        </div>

                                        <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-300">Simulation active: meme logique que le reabonnement (Mobile Money / Carte).</p>
                                        <button type="submit" class="mt-2 w-full px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium">Confirmer paiement</button>
                                    </form>
                                @endif

                                @if($isValidatedOrPaid)
                                    <div class="rounded-xl border border-blue-200 dark:border-blue-700 p-3 bg-blue-50/50 dark:bg-blue-900/10">
                                        <label class="block text-xs font-medium text-blue-800 dark:text-blue-200 mb-1">Suivi mutuelle</label>
                                        @if($facture->statut_backoffice === 'paye')
                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Demande validee et payee.</p>
                                        @else
                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Demande validee.</p>
                                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">Paiement backoffice en cours de finalisation.</p>
                                        @endif
                                    </div>
                                @elseif($isPendingSubmission)
                                    <form method="POST" action="{{ route('patient.payments.cancel-backoffice', $facture) }}" class="rounded-xl border border-orange-200 dark:border-orange-700 p-3 bg-orange-50/50 dark:bg-orange-900/10">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-xs font-medium text-orange-800 dark:text-orange-200 mb-1">Demande deja soumise</label>
                                        <p class="text-xs text-orange-700 dark:text-orange-300">Votre demande est en cours de traitement. Vous pouvez encore l'annuler.</p>
                                        <button type="submit" class="mt-2 w-full px-3 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium">Annuler la demande</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('patient.payments.submit-backoffice', $facture) }}" class="rounded-xl border border-amber-200 dark:border-amber-700 p-3 bg-amber-50/50 dark:bg-amber-900/10">
                                        @csrf
                                        @method('PATCH')
                                        <label class="block text-xs font-medium text-amber-800 dark:text-amber-200 mb-1">Soumettre au backoffice</label>
                                        <p class="text-xs text-amber-700 dark:text-amber-300">Utilise votre abonnement actif et lance le suivi backoffice.</p>
                                        <button type="submit" class="mt-2 w-full px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium">Soumettre</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune facture en attente.</p>
                    </div>
                @endforelse
            </div>

            <div>{{ $facturesEnAttente->links() }}</div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Historique recent</h3>
                <div class="mt-3 space-y-2">
                    @forelse($facturesRecentes as $facture)
                        <div class="flex items-center justify-between text-xs border-b border-gray-100 dark:border-gray-700 pb-2">
                            <span class="text-gray-700 dark:text-gray-300">{{ $facture->reference }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ ucfirst((string) $facture->statut_paiement_patient) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 dark:text-gray-400">Aucun historique.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div id="personal-payment-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 border border-slate-200 dark:border-slate-700 shadow-xl p-6">
                <p class="text-base font-semibold text-slate-900 dark:text-slate-100">Traitement du paiement personnel...</p>
                <div class="mt-5 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    <div id="personal-payment-step-validation" class="opacity-100">1. Validation des informations</div>
                    <div id="personal-payment-step-verification" class="opacity-40">2. Verification operateur / carte</div>
                    <div id="personal-payment-step-processing" class="opacity-40">3. Traitement en cours</div>
                </div>
                <div class="mt-5 h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                    <div id="personal-payment-progress-bar" class="h-full w-0 bg-emerald-600 transition-all duration-700"></div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const forms = document.querySelectorAll('.js-personal-payment-form');
                const overlay = document.getElementById('personal-payment-overlay');
                const stepValidation = document.getElementById('personal-payment-step-validation');
                const stepVerification = document.getElementById('personal-payment-step-verification');
                const stepProcessing = document.getElementById('personal-payment-step-processing');
                const progressBar = document.getElementById('personal-payment-progress-bar');

                const toggleModeFields = function (form) {
                    const modeInput = form.querySelector('.js-personal-payment-mode');
                    const mobileFields = form.querySelector('.js-personal-mobile-money-fields');
                    const cardFields = form.querySelector('.js-personal-card-fields');
                    const isMobile = modeInput && modeInput.value === 'mobile_money';

                    if (mobileFields) {
                        mobileFields.classList.toggle('hidden', !isMobile);
                    }

                    if (cardFields) {
                        cardFields.classList.toggle('hidden', isMobile);
                    }
                };

                forms.forEach(function (form) {
                    const modeInput = form.querySelector('.js-personal-payment-mode');

                    if (modeInput) {
                        modeInput.addEventListener('change', function () {
                            toggleModeFields(form);
                        });
                    }

                    toggleModeFields(form);

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
            });
        </script>
    </div>
</x-app-layout>
