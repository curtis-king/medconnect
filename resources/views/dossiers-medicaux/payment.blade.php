<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Paiement d'inscription medicale
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">Finaliser votre adhesion</h3>
                    <p class="text-blue-100 text-sm mt-1">Dossier {{ $dossier->numero_unique }}</p>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-200">Montant d'inscription</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format((float) ($dossier->frais?->prix ?? 0), 0, ',', ' ') }} XAF
                        </p>
                    </div>

                    <form method="POST" action="{{ route('user.adherer.payment.process', $dossier) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Moyen de paiement</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-yellow-400 transition">
                                    <input type="radio" name="payment_channel" value="mtn" class="mr-2" {{ old('payment_channel') === 'mtn' ? 'checked' : '' }} required>
                                    MTN Mobile Money
                                </label>
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-red-400 transition">
                                    <input type="radio" name="payment_channel" value="airtel" class="mr-2" {{ old('payment_channel') === 'airtel' ? 'checked' : '' }} required>
                                    Airtel Money
                                </label>
                                <label class="border rounded-xl p-3 cursor-pointer hover:border-blue-400 transition">
                                    <input type="radio" name="payment_channel" value="visa" class="mr-2" {{ old('payment_channel') === 'visa' ? 'checked' : '' }} required>
                                    Carte Visa
                                </label>
                            </div>
                            @error('payment_channel')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero telephone (Mobile Money)</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                                   placeholder="Ex: 6XXXXXXXX"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm">
                            @error('phone_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">Plus tard</a>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                                Payer maintenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
