@extends('layouts.app')

@section('title', 'Détails du Paiement')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Détails du Paiement</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('paiements.edit', $paiement) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Modifier
                    </a>
                    <a href="{{ route('paiements.index', ['dossier_id' => $paiement->dossier_medical_id]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Retour à la Liste
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations du Paiement -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations du Paiement</h3>

                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type de Paiement</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($paiement->type_paiement) }}</dd>
                            </div>

                            @if($paiement->fraisInscription)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Frais d'Inscription</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->fraisInscription->libelle }} - {{ number_format($paiement->fraisInscription->montant, 2, ',', ' ') }}€</dd>
                            </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Montant</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ $paiement->montant_formatted }}</dd>
                            </div>

                            @if($paiement->type_paiement === 'reabonnement')
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre de Mois</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->nombre_mois }}</dd>
                            </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($paiement->statut === 'paye') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($paiement->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mode de Paiement</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</dd>
                            </div>

                            @if($paiement->reference_paiement)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Référence de Paiement</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->reference_paiement }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Périodes et Dates -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Périodes et Dates</h3>

                        <dl class="space-y-3">
                            @if($paiement->periode_debut)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Période Début</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->periode_debut->format('d/m/Y') }}</dd>
                            </div>
                            @endif

                            @if($paiement->periode_fin)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Période Fin</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->periode_fin->format('d/m/Y') }}</dd>
                            </div>
                            @endif

                            @if($paiement->date_encaissement)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date d'Encaissement</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->date_encaissement->format('d/m/Y H:i') }}</dd>
                            </div>
                            @endif

                            @if($paiement->date_echeance)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date d'Échéance</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->date_echeance->format('d/m/Y') }}</dd>
                            </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Créé le</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->created_at->format('d/m/Y H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dernière Modification</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informations Associées -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations Associées</h3>

                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($paiement->dossierMedical)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dossier Médical</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('dossier-medicals.show', $paiement->dossierMedical) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $paiement->dossierMedical->nom }} {{ $paiement->dossierMedical->prenom }}
                                    </a>
                                </dd>
                            </div>
                            @endif

                            @if($paiement->encaissePar)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Encaissé par</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $paiement->encaissePar->name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    @if($paiement->notes)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Notes</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{ $paiement->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-4">
                        @if($paiement->statut === 'en_attente')
                        <form action="{{ route('paiements.update', $paiement) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="statut" value="paye">
                            <input type="hidden" name="date_encaissement" value="{{ now()->format('Y-m-d\TH:i') }}">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Marquer comme Payé
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('paiements.destroy', $paiement) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
