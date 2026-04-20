<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestion Demande #{{ $demande->id }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('demandes-services.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Détails de la demande</h3>
                            @switch($demande->statut)
                                @case('en_attente')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">En attente</span>
                                    @break
                                @case('valide')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">Validé</span>
                                    @break
                                @case('rejete')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Rejeté</span>
                                    @break
                                @case('termine')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Terminé</span>
                                    @break
                            @endswitch
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Service</p>
                                <p class="font-semibold">{{ $demande->service->nom }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Type</p>
                                <p class="font-semibold">{{ $demande->service->type }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Prix</p>
                                <p class="font-semibold text-teal-600">{{ number_format($demande->service->prix, 0, ',', ' ') }} Fcfa</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Date création</p>
                                <p class="font-semibold">{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($demande->notes)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-gray-500 text-sm">Notes client</p>
                            <p>{{ $demande->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Client</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Nom</p>
                                <p class="font-semibold">{{ $demande->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Email</p>
                                <p class="font-semibold">{{ $demande->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Téléphone</p>
                                <p class="font-semibold">
                                    @if($demande->user->phone)
                                    <a href="tel:{{ $demande->user->phone }}" class="text-teal-600 hover:underline">{{ $demande->user->phone }}</a>
                                    @else
                                    Non défini
                                    @endif
                                </p>
                            </div>
                            @if($demande->dossier)
                            <div>
                                <p class="text-gray-500">Dossier</p>
                                <p class="font-semibold">{{ $demande->dossier->numero_unique }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($adresseComplete || $hasLocation)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Localisation</h3>
                            @if($hasLocation)
                            <a href="{{ $gpsUrl }}" target="_blank" class="text-sm text-teal-600 hover:text-teal-700 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Navigator GPS
                            </a>
                            @endif
                        </div>

                        @if($adresseComplete)
                        <div class="mb-4">
                            <p class="text-gray-500 text-sm">Adresse</p>
                            <p class="font-semibold">{{ $adresseComplete }}</p>
                        </div>
                        @endif

                        @if($hasLocation)
                        <div class="relative rounded-lg overflow-hidden h-64">
                            <iframe
                                src="{{ $mapUrl }}"
                                width="100%"
                                height="100%"
                                style="border:0"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            <div class="absolute bottom-2 right-2 bg-white dark:bg-gray-800 px-2 py-1 rounded text-xs shadow">
                                🌍 {{ $demande->user->latitude }}, {{ $demande->user->longitude }}
                            </div>
                        </div>
                        @else
                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 flex items-center gap-3">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-orange-700 dark:text-orange-300">Localis GPS non disponible pour ce client</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Pièces jointes</h3>
                            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                + Ajouter
                            </button>
                        </div>
                        @if($demande->piecesJointes->count() > 0)
                            <div class="space-y-2">
                                @foreach($demande->piecesJointes as $pj)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-sm">{{ $pj->nom_fichier }}</p>
                                            <p class="text-xs text-gray-500">{{ $pj->type }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewFile('{{ $pj->id }}')" class="text-teal-600 hover:text-teal-700 p-1" title="Voir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <a href="{{ asset('storage/' . $pj->chemin_fichier) }}" target="_blank" class="text-teal-600 hover:text-teal-700 p-1" title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucune pièce jointe</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Décisions</h3>
                        </div>
                        @if($demande->decisions->count() > 0)
                            <div class="space-y-3">
                                @foreach($demande->decisions as $decision)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-sm capitalize">{{ $decision->type }}</span>
                                        <span class="text-xs text-gray-500">{{ $decision->taken_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($decision->motif)
                                    <p class="text-sm mt-1">{{ $decision->motif }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">Par: {{ $decision->takenBy->name }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucune décision</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Rendez-vous</h3>
                            <button onclick="document.getElementById('rdvModal').classList.remove('hidden')" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                + Planifier
                            </button>
                        </div>
                        @if($demande->rendezVous->count() > 0)
                            <div class="space-y-3">
                                @foreach($demande->rendezVous as $rdv)
                                <div class="p-4 border rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $rdv->date_rendez_vous->format('d/m/Y à H:i') }}</span>
                                        </div>
                                        @switch($rdv->status)
                                            @case('planifie')
                                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Planifié</span>
                                                @break
                                            @case('confirme')
                                                <span class="px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-800">Confirmé</span>
                                                @break
                                            @case('annule')
                                                <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">Annulé</span>
                                                @break
                                            @case('realise')
                                                <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Réalisé</span>
                                                @break
                                        @endswitch
                                    </div>
                                    @if($rdv->lieu)
                                    <p class="text-sm text-gray-600">{{ $rdv->lieu }}</p>
                                    @endif
                                    @if($rdv->notes)
                                    <p class="text-sm mt-2">{{ $rdv->notes }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucun rendez-vous</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Factures</h3>
                            <button onclick="document.getElementById('factureModal').classList.remove('hidden')" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                                + Créer facture
                            </button>
                        </div>
                        @if($demande->factures->count() > 0)
                            <div class="space-y-3">
                                @foreach($demande->factures as $facture)
                                <div class="p-4 border rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold">{{ $facture->numero_facture }}</p>
                                            <p class="text-2xl font-bold text-teal-600">{{ number_format($facture->montant, 0, ',', ' ') }} Fcfa</p>
                                        </div>
                                        <div class="text-right">
                                            @switch($facture->statut)
                                                @case('en_attente')
                                                    <span class="px-2 py-1 rounded text-xs bg-orange-100 text-orange-800">En attente</span>
                                                    @break
                                                @case('paye')
                                                    <span class="px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-800">Payé</span>
                                                    @break
                                                @case('annule')
                                                    <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">Annulé</span>
                                                    @break
                                            @endswitch
                                            @if($facture->date_echeance)
                                            <p class="text-xs text-gray-500 mt-1">Échéance: {{ $facture->date_echeance->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucune facture</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($demande->statut === 'en_attente')
                            <form method="POST" action="{{ route('demandes-services.valider', $demande) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Valider la demande
                                </button>
                            </form>
                            @endif

                            @if($demande->statut !== 'rejete' && $demande->statut !== 'termine')
                            <form method="POST" action="{{ route('demandes-services.rejeter', $demande) }}">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-2">
                                    <input type="text" name="reponse" placeholder="Motif..." required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Rejeter
                                    </button>
                                </div>
                            </form>
                            @endif

                            @if($demande->statut === 'valide')
                            <form method="POST" action="{{ route('demandes-services.terminer', $demande) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Marquer terminé
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    @if($demande->reponse_backoffice || $demande->traite_par_user_id)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Réponse</h3>
                        @if($demande->reponse_backoffice)
                        <p class="mb-4">{{ $demande->reponse_backoffice }}</p>
                        @endif
                        @if($demande->traitePar)
                        <p class="text-sm text-gray-500">Traitée par {{ $demande->traitePar->name }} le {{ $demande->traite_le->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="uploadModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Ajouter une pièce jointe</h3>
            <form method="POST" action="{{ route('demandes-services.piece-jointe', $demande) }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Type</label>
                        <select name="type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                            <option value="document">Document</option>
                            <option value="prescription">Prescription</option>
                            <option value="certificat">Certificat</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fichier</label>
                        <input type="file" name="fichier" required class="w-full">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</button>
                    <button type="submit" class="flex-1 bg-teal-500 text-white px-4 py-2 rounded-lg">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rdvModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Planifier un rendez-vous</h3>
            <form method="POST" action="{{ route('demandes-services.rendez-vous', $demande) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Date et heure</label>
                        <input type="datetime-local" name="date_rendez_vous" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Lieu</label>
                        <input type="text" name="lieu" placeholder="Nom du lieu" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Adresse</label>
                        <textarea name="adresse" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('rdvModal').classList.add('hidden')" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</button>
                    <button type="submit" class="flex-1 bg-teal-500 text-white px-4 py-2 rounded-lg">Planifier</button>
                </div>
            </form>
        </div>
    </div>

    <div id="factureModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Créer une facture</h3>
            <form method="POST" action="{{ route('demandes-services.facture', $demande) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Montant</label>
                        <input type="number" name="montant" value="{{ $demande->service->prix }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Date échéance</label>
                        <input type="date" name="date_echeance" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-700"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('factureModal').classList.add('hidden')" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</button>
                    <button type="submit" class="flex-1 bg-teal-500 text-white px-4 py-2 rounded-lg">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewFileModal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 w-full max-w-4xl h-[80vh] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Visualisation</h3>
                <button onclick="document.getElementById('viewFileModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <iframe id="fileViewer" class="flex-1 w-full rounded-lg" style="border: none;"></iframe>
        </div>
    </div>

    <script>
    const piecesJointes = @json($demande->piecesJointes->keyBy('id'));

    function viewFile(id) {
        const pj = piecesJointes[id];
        if (pj) {
            const modal = document.getElementById('viewFileModal');
            const iframe = document.getElementById('fileViewer');
            
            const filePath = '/storage/' + pj.chemin_fichier;
            if (pj.mime_type && pj.mime_type.startsWith('image/')) {
                iframe.innerHTML = '<img src="' + filePath + '" class="w-full h-full object-contain" />';
            } else if (pj.mime_type === 'application/pdf') {
                iframe.src = filePath;
            } else {
                iframe.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100"><a href="' + filePath + '" download class="px-4 py-2 bg-teal-600 text-white rounded-lg">Télécharger le fichier</a></div>';
            }
            
            modal.classList.remove('hidden');
        }
    }
    </script>
</x-app-layout>