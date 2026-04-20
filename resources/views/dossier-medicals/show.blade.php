<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dossier Médical - ') . $dossier->numero_unique }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Détails du Dossier</h3>
                        <div class="space-x-2">
                            <a href="{{ route('paiements.create', ['dossier_id' => $dossier->id]) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Ajouter un Paiement
                            </a>
                            <a href="{{ route('paiements.index', ['dossier_id' => $dossier->id]) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Voir les Paiements
                            </a>
                            <a href="{{ route('dossier-medicals.edit', $dossier->id) }}"
                               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200">
                                Modifier
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">N° Dossier</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->numero_unique }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Source de Création</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($dossier->source_creation) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom Complet</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->nom_complet }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Naissance</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $dossier->date_naissance ? $dossier->date_naissance->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexe</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $dossier->sexe === 'M' ? 'Masculin' : ($dossier->sexe === 'F' ? 'Féminin' : 'N/A') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->telephone ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Groupe Sanguin</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->groupe_sanguin ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frais d'Inscription</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $dossier->fraisInscription->libelle ?? 'N/A' }} ({{ $dossier->fraisInscription->prix ?? 'N/A' }}€)
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut Paiement</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                    @if($dossier->statut_paiement_inscription === 'paye') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($dossier->statut_paiement_inscription === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($dossier->statut_paiement_inscription === 'exonere') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $dossier->statut_paiement_inscription)) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mode de Paiement</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $dossier->mode_paiement_inscription ? ucfirst(str_replace('_', ' ', $dossier->mode_paiement_inscription)) : 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Référence Paiement</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->reference_paiement_inscription ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actif</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                    @if($dossier->actif) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ $dossier->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Créé le</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dernière modification</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $dossier->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->adresse ?? 'Adresse non renseignée' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allergies</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->allergies ?? 'Aucune allergie connue' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maladies Chroniques</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->maladies_chroniques ?? 'Aucune maladie chronique connue' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Traitements en Cours</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->traitements_en_cours ?? 'Aucun traitement en cours' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Antécédents Familiaux</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->antecedents_familiaux ?? 'Aucun antécédent familial connu' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Antécédents Personnels</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $dossier->antecedents_personnels ?? 'Aucun antécédent personnel connu' }}</p>
                            </div>
                        </div>

                        @if($dossier->contact_urgence_nom)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact d'Urgence</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                    <strong>{{ $dossier->contact_urgence_nom }}</strong><br>
                                    Téléphone: {{ $dossier->contact_urgence_telephone ?? 'N/A' }}<br>
                                    Relation: {{ $dossier->contact_urgence_relation ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Historique des Paiements</h4>
                        @if($dossier->paiements->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Montant</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Période</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($dossier->paiements->take(5) as $paiement)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ ucfirst($paiement->type_paiement) }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $paiement->montant_formatted }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $paiement->periode_debut->format('d/m/Y') }} - {{ $paiement->periode_fin->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($paiement->statut === 'paye') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($paiement->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $paiement->date_encaissement ? $paiement->date_encaissement->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($dossier->paiements->count() > 5)
                                <div class="mt-4">
                                    <a href="{{ route('paiements.index', ['dossier_id' => $dossier->id]) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        Voir tous les paiements ({{ $dossier->paiements->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 dark:text-gray-400">Aucun paiement enregistré pour ce dossier.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
