<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nouvel Abonnement — {{ $dossierProfessionnel->raison_sociale ?? $dossierProfessionnel->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h3 class="text-white font-semibold text-lg">Abonnement Mensuel Professionnel</h3>
                    <p class="text-blue-100 text-sm mt-1">
                        Licence : <span class="font-mono font-semibold">{{ $dossierProfessionnel->numero_licence ?? '—' }}</span>
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('subscriptions-pro.store', $dossierProfessionnel) }}" class="space-y-4" id="subForm">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif de Réabonnement <span class="text-red-500">*</span></label>
                            <select name="frais_id" id="fraisId" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach($fraisReabonnement as $frais)
                                    <option value="{{ $frais->id }}" @selected(old('frais_id') == $frais->id)>
                                        {{ $frais->libelle }} — {{ number_format($frais->prix, 0, ',', ' ') }} XAF / mois
                                    </option>
                                @endforeach
                            </select>
                            @error('frais_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de Mois <span class="text-red-500">*</span></label>
                                <input type="number" name="nombre_mois" id="nombreMois" value="{{ old('nombre_mois', 1) }}" required min="1" max="12"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                @error('nombre_mois')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mode de Paiement <span class="text-red-500">*</span></label>
                                <select name="mode_paiement" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="cash" @selected(old('mode_paiement') === 'cash')>Cash</option>
                                    <option value="mobile_money" @selected(old('mode_paiement') === 'mobile_money')>Mobile Money</option>
                                    <option value="virement" @selected(old('mode_paiement') === 'virement')>Virement</option>
                                    <option value="carte" @selected(old('mode_paiement') === 'carte')>Carte</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Référence de Paiement</label>
                            <input type="text" name="reference_paiement" value="{{ old('reference_paiement') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Résumé calculé -->
                        <div id="summary" class="hidden bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 text-sm">
                            <h5 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Résumé</h5>
                            <div class="grid grid-cols-2 gap-2 text-blue-700 dark:text-blue-300">
                                <div>Début :</div><div id="summaryDebut" class="font-medium">—</div>
                                <div>Fin :</div><div id="summaryFin" class="font-medium">—</div>
                                <div>Montant total :</div><div id="summaryMontant" class="font-bold text-blue-900 dark:text-blue-100">—</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('subscriptions-pro.index', $dossierProfessionnel) }}"
                               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2 rounded-lg text-sm transition">
                                Annuler
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm transition">
                                Créer l'Abonnement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const calculerUrl = "{{ route('subscriptions-pro.calculer', $dossierProfessionnel) }}";
        const csrfToken = "{{ csrf_token() }}";

        function calculer() {
            const fraisId = document.getElementById('fraisId').value;
            const nombreMois = document.getElementById('nombreMois').value;

            if (!fraisId || !nombreMois) return;

            fetch(calculerUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ frais_id: fraisId, nombre_mois: nombreMois })
            })
            .then(r => r.json())
            .then(data => {
                if (data.date_debut) {
                    document.getElementById('summaryDebut').textContent = data.date_debut;
                    document.getElementById('summaryFin').textContent = data.date_fin;
                    document.getElementById('summaryMontant').textContent = data.montant_formatted;
                    document.getElementById('summary').classList.remove('hidden');
                }
            });
        }

        document.getElementById('fraisId').addEventListener('change', calculer);
        document.getElementById('nombreMois').addEventListener('input', calculer);
    </script>
</x-app-layout>
