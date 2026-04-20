<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Générer un rapport d'optimisation</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configurez les paramètres d'analyse IA et lancez l'optimisation</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.optimization-reports.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="stale_invoice_days" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Seuil factures obsolètes (jours)</label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Après combien de jours une facture est-elle considérée comme obsolète?</p>
                            <input type="number" name="stale_invoice_days" id="stale_invoice_days" value="{{ old('stale_invoice_days', $defaults['stale_invoice_days']) }}" min="1" max="90" class="mt-3 w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="stale_backoffice_days" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Seuil backoffice en retard (jours)</label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Après combien de jours une soumission est-elle considérée comme en retard?</p>
                            <input type="number" name="stale_backoffice_days" id="stale_backoffice_days" value="{{ old('stale_backoffice_days', $defaults['stale_backoffice_days']) }}" min="1" max="90" class="mt-3 w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="upcoming_window_days" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Fenêtre RDV à venir (jours)</label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Combien de jours à l'avance analyser les rendez-vous?</p>
                            <input type="number" name="upcoming_window_days" id="upcoming_window_days" value="{{ old('upcoming_window_days', $defaults['upcoming_window_days']) }}" min="1" max="30" class="mt-3 w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Provider IA</label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Quel fournisseur IA utiliser?</p>
                            <select name="provider" id="provider" class="mt-3 w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="gemini" {{ old('provider', $defaults['provider']) === 'gemini' ? 'selected' : '' }}>Gemini (gratuit)</option>
                                <option value="openai" {{ old('provider', $defaults['provider']) === 'openai' ? 'selected' : '' }}>OpenAI (payant)</option>
                                <option value="anthropic" {{ old('provider', $defaults['provider']) === 'anthropic' ? 'selected' : '' }}>Anthropic Claude (payant)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <strong>ℹ️ Info:</strong> L'IA va analyser ta base de données, identifier les problèmes (factures oubliées, RDV en retard, soumissions bloquées) et proposer des actions prioritaires.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                            🚀 Générer le rapport
                        </button>
                        <a href="{{ route('admin.optimization-reports.index') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
