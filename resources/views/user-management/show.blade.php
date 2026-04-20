<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails de l\'Utilisateur') }}
            </h2>
            <div class="mt-2 flex justify-center space-x-4">
                <a href="{{ route('user-management.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                    ← Retour à la liste
                </a>
                @if(isset($user) && $user && isset($user->id))
                <a href="{{ route('user-management.edit', $user) }}"
                   class="bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 px-4 py-2 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800 transition duration-200 text-sm font-medium">
                    Modifier
                </a>
                @else
                <span class="text-red-500 text-sm">Erreur: Utilisateur non disponible</span>
                @endif
                @if($user->id !== auth()->id())
                    <form action="{{ route('user-management.toggle-status', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg transition duration-200 text-sm font-medium
                                       {{ $user->isActive() ?
                                          'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800' :
                                          'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800' }}">
                            {{ $user->isActive() ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <!-- Header with gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 p-8 text-white text-center">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-2xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="text-left">
                            <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                            <p class="text-blue-100 text-lg">{{ $user->email }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($user->role === 'admin') bg-red-500/20 text-red-100
                                    @elseif($user->role === 'professional') bg-purple-500/20 text-purple-100
                                    @else bg-blue-500/20 text-blue-100 @endif">
                                    {{ $roles[$user->role] ?? $user->role }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($user->isActive()) bg-green-500/20 text-green-100
                                    @else bg-red-500/20 text-red-100 @endif">
                                    {{ $user->isActive() ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informations principales -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Informations générales</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Nom complet :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Email :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->email }}</span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Rôle :</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                            @if($user->role === 'admin') bg-red-100 text-red-800
                                            @elseif($user->role === 'professional') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $roles[$user->role] ?? $user->role }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Statut :</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                            @if($user->isActive()) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $user->isActive() ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Date de création :</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : 'N/A' }}</span>
                                    </div>

                                    @if($user->updated_at && $user->updated_at != $user->created_at)
                                        <div class="flex justify-between items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                            <span class="font-medium text-gray-600 dark:text-gray-400">Dernière modification :</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Profil et statistiques -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Profil</h3>
                                @if($user->profile)
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $user->profile }}</p>
                                    </div>
                                @else
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">Aucune description de profil fournie</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Statistiques -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Statistiques</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center border border-blue-200 dark:border-blue-800">
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">#{{ $user->id }}</div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400">ID utilisateur</div>
                                    </div>
                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center border border-green-200 dark:border-green-800">
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                            @if($user->role === 'admin') A
                                            @elseif($user->role === 'professional') P
                                            @else U @endif
                                        </div>
                                        <div class="text-sm text-green-600 dark:text-green-400">Type de compte</div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Conformite IA / Admin</h3>

                                @if(($medicalComplianceReviews ?? collect())->isNotEmpty())
                                    <div class="space-y-3 mb-4">
                                        @foreach($medicalComplianceReviews as $medicalReview)
                                            @php
                                                $risk = (string) ($medicalReview['review']['risk_level'] ?? 'low');
                                            @endphp
                                            <div class="p-4 rounded-lg border @if($risk === 'high') border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20 @elseif($risk === 'medium') border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20 @else border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20 @endif">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    Dossier medical {{ $medicalReview['dossier']->numero_unique }}
                                                    | Risque {{ strtoupper($risk) }}
                                                    | Score {{ (int) ($medicalReview['review']['score'] ?? 0) }}
                                                </p>
                                                @if(!empty($medicalReview['review']['reasons']))
                                                    <ul class="mt-2 list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                        @foreach(($medicalReview['review']['reasons'] ?? []) as $reason)
                                                            <li>{{ $reason }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($professionalComplianceReview)
                                    @php
                                        $proRisk = (string) ($professionalComplianceReview['risk_level'] ?? 'low');
                                    @endphp
                                    <div class="p-4 rounded-lg border @if($proRisk === 'high') border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20 @elseif($proRisk === 'medium') border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20 @else border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20 @endif">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                                            Dossier professionnel | Risque {{ strtoupper($proRisk) }} | Score {{ (int) ($professionalComplianceReview['score'] ?? 0) }}
                                        </p>
                                        @if(!empty($professionalComplianceReview['reasons']))
                                            <ul class="mt-2 list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                @foreach(($professionalComplianceReview['reasons'] ?? []) as $reason)
                                                    <li>{{ $reason }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif

                                @if(($medicalComplianceReviews ?? collect())->isEmpty() && !$professionalComplianceReview)
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-300">
                                        Aucun dossier medical/professionnel rattache pour controle de conformite.
                                    </div>
                                @endif
                            </div>

                            <!-- Actions rapides -->
                            @if($user->id !== auth()->id())
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Actions rapides</h3>
                                    <div class="space-y-3">
                                        <form action="{{ route('user-management.toggle-status', $user) }}" method="POST" class="inline-block w-full">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-full px-4 py-3 rounded-lg transition duration-200 font-medium
                                                           {{ $user->isActive() ?
                                                              'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800' :
                                                              'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800' }}">
                                                <span class="flex items-center justify-center space-x-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="{{ $user->isActive() ? 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                                    </svg>
                                                    <span>{{ $user->isActive() ? 'Désactiver le compte' : 'Activer le compte' }}</span>
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
