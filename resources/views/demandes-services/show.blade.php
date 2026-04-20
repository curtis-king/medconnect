<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Demande') }} #{{ $demande->id }}
            </h2>
            <a href="{{ route('demandes-services.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">Informations Client</h3>
                <p><strong>Nom:</strong> {{ $demande->user->name }}</p>
                <p><strong>Email:</strong> {{ $demande->user->email }}</p>
                <p><strong>Téléphone:</strong> {{ $demande->user->phone ?? 'Non défini' }}</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">Service</h3>
                <p><strong>Service:</strong> {{ $demande->service->nom }}</p>
                <p><strong>Type:</strong> {{ $demande->service->type }}</p>
                <p><strong>Prix:</strong> {{ number_format($demande->service->prix, 0, ',', ' ') }} Fcfa</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">Statut</h3>
                @switch($demande->statut)
                    @case('en_attente')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">En attente</span>
                        @break
                    @case('valide')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Validé</span>
                        @break
                    @case('rejete')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejeté</span>
                        @break
                    @case('termine')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Terminé</span>
                        @break
                @endswitch
                <p class="mt-2"><strong>Créé le:</strong> {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
                @if($demande->traite_le)
                <p><strong>Traité le:</strong> {{ $demande->traite_le->format('d/m/Y à H:i') }}</p>
                @endif
            </div>

            @if($demande->traitePar)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">Traité Par</h3>
                <p>{{ $demande->traitePar->name }}</p>
            </div>
            @endif
        </div>

        @if($demande->notes)
        <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Notes du client</h3>
            <p>{{ $demande->notes }}</p>
        </div>
        @endif

        @if($demande->reponse_backoffice)
        <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg">
            <h3 class="font-semibold mb-2 text-emerald-600 dark:text-emerald-400">Réponse</h3>
            <p>{{ $demande->reponse_backoffice }}</p>
        </div>
        @endif

        @if($demande->statut === 'en_attente')
        <div class="flex gap-4">
            <form method="POST" action="{{ route('demandes-services.valider', $demande) }}" class="flex-1">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Valider
                </button>
            </form>

            <form method="POST" action="{{ route('demandes-services.rejeter', $demande) }}" class="flex-1">
                @csrf
                @method('PATCH')
                <div class="flex">
                    <input type="text" name="reponse" placeholder="Motif du rejet..." required class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 rounded-r-lg flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Rejeter
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('demandes-services.terminer', $demande) }}" class="flex-1">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Terminer
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>