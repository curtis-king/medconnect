<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Devenir professionnel - Etape 2/2</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Paiement inscription professionnelle</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">Formulaire de paiement utilisateur professionnel</h3>
                    <p class="text-emerald-100 text-sm mt-1">Validation finale par un administrateur apres verification.</p>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-200">Montant a payer</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format((float) ($dossierProfessionnel->frais?->prix ?? 0), 0, ',', ' ') }} XAF
                        </p>
                    </div>

                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-900/20 p-4">
                        <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">Récapitulatif professionnel</h4>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-300">Spécialité:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700">
                                {{ $dossierProfessionnel->specialite ?? '—' }}
                            </span>
                            <span class="text-gray-600 dark:text-gray-300 ml-2">Type:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $dossierProfessionnel->type_structure_label }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user.professional.payment.process', $dossierProfessionnel) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Moyen de paiement *</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-yellow-400 transition">
                                    <input type="radio" name="payment_channel" value="mtn" class="mr-2" required>
                                    MTN Mobile Money
                                </label>
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-red-400 transition">
                                    <input type="radio" name="payment_channel" value="airtel" class="mr-2" required>
                                    Airtel Money
                                </label>
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-blue-400 transition">
                                    <input type="radio" name="payment_channel" value="visa" class="mr-2" required>
                                    Carte Visa
                                </label>
                            </div>
                            @error('payment_channel')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero telephone (MTN/Airtel)</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="Ex: 6XXXXXXXX"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                        </div>

                        <div id="visa_fields" class="hidden rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/60 dark:bg-emerald-900/20 p-4 space-y-3">
                            <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">Informations Carte Visa</h4>
                            <div>
                                <label for="card_holder_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du titulaire</label>
                                <input id="card_holder_name" type="text" name="card_holder_name" value="{{ old('card_holder_name') }}"
                                       placeholder="Nom comme sur la carte"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                                @error('card_holder_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero de carte</label>
                                <input id="card_number" type="text" name="card_number" value="{{ old('card_number') }}"
                                       placeholder="4111 1111 1111 1111"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                                @error('card_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="card_expiry_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mois expiration</label>
                                    <input id="card_expiry_month" type="number" min="1" max="12" name="card_expiry_month" value="{{ old('card_expiry_month') }}"
                                           placeholder="MM"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                                    @error('card_expiry_month')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="card_expiry_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Annee expiration</label>
                                    <input id="card_expiry_year" type="number" min="{{ now()->year }}" max="{{ now()->year + 20 }}" name="card_expiry_year" value="{{ old('card_expiry_year') }}"
                                           placeholder="YYYY"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                                    @error('card_expiry_year')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="card_cvv" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CVV</label>
                                <input id="card_cvv" type="password" maxlength="4" name="card_cvv" value="{{ old('card_cvv') }}"
                                       placeholder="***"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                                @error('card_cvv')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Plus tard</a>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium">
                                Confirmer le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentRadios = document.querySelectorAll('input[name="payment_channel"]');
        const visaFields = document.getElementById('visa_fields');

        function togglePaymentFields() {
            const selected = document.querySelector('input[name="payment_channel"]:checked')?.value;
            const isVisa = selected === 'visa';

            visaFields.classList.toggle('hidden', !isVisa);
            document.getElementById('phone_number').closest('div').classList.toggle('hidden', isVisa);
        }

        paymentRadios.forEach((radio) => radio.addEventListener('change', togglePaymentFields));
        togglePaymentFields();
    </script>
</x-app-layout>
