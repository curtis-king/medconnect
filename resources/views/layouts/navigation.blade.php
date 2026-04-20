<nav x-data="{ open: false }" class="relative z-50">
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.5); }
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .nav-glass {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(71, 85, 105, 0.3);
        }

        .nav-link-modern {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link-modern:hover {
            transform: translateY(-1px);
        }

        .nav-link-modern::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link-modern:hover::after {
            width: 100%;
        }

        .user-btn-modern {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }

        .user-btn-modern:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.2));
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .mobile-menu {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .dark .mobile-menu {
            background: rgba(15, 23, 42, 0.98);
            border-top: 1px solid rgba(71, 85, 105, 0.3);
        }
    </style>

    <!-- Primary Navigation Menu -->
    <div class="nav-glass sticky top-0 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-18">
                <!-- Sidebar Toggle + Logo Section -->
                <div class="flex items-center space-x-3">
                    <!-- Hamburger Button pour ouvrir le sidebar -->
                    @auth
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:text-blue-400 dark:hover:bg-slate-700/50 transition-all duration-200"
                            title="Menu rapide">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @endauth
                    <div class="shrink-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                            <div class="w-15 h-10 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-105">
                                <x-application-logo class="h-6 w-6 text-white" />
                            </div>
                            <div class="hidden sm:block">
                                <h1 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">
                                    {{ config('app.name', 'MEDCONNECT') }}
                                </h1>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    @auth
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                </svg>
                                <span>{{ __('Dashboard') }}</span>
                            </span>
                        </a>

                        <!-- Gestion Client Dropdown -->
                        @if(Auth::user()->role === 'admin')
                        <div class="relative">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs(['dossier-medicals.*', 'paiements.*']) ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>{{ __('Gestion Client') }}</span>
                                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-slot>



                                <x-slot name="content">
                                    <div class="py-2">
                                        <x-dropdown-link :href="route('dossier-medicals.create')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            <span>{{ __('Inscription') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('subscriptions.create')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span>{{ __('Réabonnement') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('dossier-medicals.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>{{ __('Liste Dossiers') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('dossier-medicals.pending-validation')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ __('Patients En Attente') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('carte-medicale.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                            </svg>
                                            <span>{{ __('Carte Médicale') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('controle-client.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            <span>{{ __('Contrôle Client') }}</span>
                                        </x-dropdown-link>

                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif
                        @if(Auth::user()->role === 'admin')
                        <div class="relative">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs(['frais.*', 'frais-inscriptions.*', 'taux-reductions.*']) ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ __('Paramètres') }}</span>
                                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="py-2">
                                        <x-dropdown-link :href="route('frais.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <span>{{ __('Frais') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('frais-inscriptions.index')" class="flex items-center space-x-3 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 8h.01M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                                            </svg>
                                            <span>{{ __("Frais d'inscription") }}</span>
                                        </x-dropdown-link>

                                        @if(Auth::user()->role === 'admin')
                                        <x-dropdown-link :href="route('taux-reductions.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            <span>{{ __('Taux Réduction') }}</span>
                                        </x-dropdown-link>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif

                        <!-- Gestion Utilisateurs Dropdown - Admin seulement -->
                        @if(Auth::user()->role === 'admin')
                        <div class="relative">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('dossier-professionnels.*') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ __('Professionnels') }}</span>
                                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="py-2">
                                        <x-dropdown-link :href="route('dossier-professionnels.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>{{ __('Dossiers Professionnels') }}</span>
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('dossier-professionnels.create')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span>{{ __('Nouveau Dossier Pro') }}</span>
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('dossier-professionnels.pending-validation')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ __('En Attente de Validation') }}</span>
                                        </x-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif

                        <!-- Gestion Utilisateurs Dropdown - Admin seulement -->
                        @if(Auth::user()->role === 'admin')
                        <div class="relative">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('user-management.*') ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                            <span>{{ __('Utilisateurs') }}</span>
                                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="py-2">
                                        <x-dropdown-link :href="route('user-management.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ __('Tous les utilisateurs') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('user-management.create')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                            <span>{{ __('Nouvel utilisateur') }}</span>
                                        </x-dropdown-link>

                                        <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                                        <div class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase">Filtrer par rôle</div>

                                        <x-dropdown-link :href="route('user-management.index', ['role' => 'livreur'])" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                            <span>{{ __('Livreurs') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('user-management.index', ['role' => 'soignant'])" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span>{{ __('Soignants') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('user-management.index', ['role' => 'financiere'])" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>{{ __('Agents Financiers') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('user-management.index', ['role' => 'service_client'])" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <span>{{ __('Service Client') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('user-management.index', ['role' => 'membre'])" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ __('Membres Mutualisation') }}</span>
                                        </x-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif

                         <!-- Gestion financière - Admin seulement -->
                        @if(Auth::user()->role === 'admin')
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="nav-link-modern px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:text-blue-600 dark:hover:text-blue-400 {{ request()->routeIs('paiements.*') || request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : '' }} inline-flex items-center">
                                        <span class="flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ __('Finances') }}</span>
                                        </span>
                                        <svg class="ms-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="py-1">
                                        <x-dropdown-link :href="route('transactions.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span>{{ __('Historique Transactions') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('paiements.index')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>{{ __('Paiements') }}</span>
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('paiements.online-history')" class="flex items-center space-x-3">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>{{ __('Paiements en ligne') }}</span>
                                        </x-dropdown-link>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif
                    </div>
                    @endauth
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-3">
                    @auth
                    <!-- User Dropdown -->
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="user-btn-modern inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none transition-all duration-300">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-green-500 rounded-md flex items-center justify-center text-white font-semibold text-sm">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <div class="text-left hidden md:block">
                                            <div class="font-medium text-sm">{{ Auth::user()->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
                                                    @if(Auth::user()->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                                    @elseif(Auth::user()->role === 'professional') bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400
                                                    @else bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 @endif">
                                                    @if(Auth::user()->role === 'admin') 👑 Admin
                                                    @elseif(Auth::user()->role === 'professional') 🔧 Pro
                                                    @else 👤 User @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <svg class="ms-1 h-4 w-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="py-2">
                                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center space-x-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ __('Profile') }}</span>
                                    </x-dropdown-link>

                                    <hr class="my-2 border-gray-200 dark:border-gray-600">

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="flex items-center space-x-3 text-red-600 dark:text-red-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>{{ __('Log Out') }}</span>
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @else
                    <!-- Guest Links -->
                    <div class="hidden sm:flex sm:items-center space-x-4">
                        <a href="{{ route('login') }}" class="nav-link-modern text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                            {{ __('Connexion') }}
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            {{ __('Inscription') }}
                        </a>
                    </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all duration-200">
                            <svg class="h-6 w-6" :class="{'hidden': open, 'block': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg class="h-6 w-6" :class="{'block': open, 'hidden': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mobile-menu md:hidden border-t">
            <div class="px-4 py-5 space-y-3">
                @auth
                <!-- Mobile Navigation Links -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    <span class="font-medium text-sm">{{ __('Dashboard') }}</span>
                </a>

                <!-- Gestion Client Section Mobile -->
                @if(Auth::user()->role === 'admin')
                <div x-data="{ clientOpen: false }" class="space-y-1">
                    <button @click="clientOpen = !clientOpen"
                            class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs(['dossier-medicals.*', 'paiements.*']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ __('Gestion Client') }}</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': clientOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="clientOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-40" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-40" x-transition:leave-end="opacity-0 max-h-0" class="ml-6 space-y-1 overflow-hidden">
                        <a href="{{ route('dossier-medicals.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('dossier-medicals.create') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span class="text-sm">{{ __('Inscription') }}</span>
                        </a>
                        <a href="{{ route('subscriptions.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('subscriptions.create') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-sm">{{ __('Réabonnement') }}</span>
                        </a>
                        <a href="{{ route('dossier-medicals.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('dossier-medicals.index') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm">{{ __('Déclaration Carte Médicale') }}</span>
                        </a>
                        <a href="{{ route('dossier-medicals.pending-validation') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('dossier-medicals.pending-validation') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">{{ __('Patients En Attente') }}</span>
                        </a>
                        <a href="{{ route('controle-client.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('controle-client.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="text-sm">{{ __('Contrôle Client') }}</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Paramètres Section Mobile - Selon le rôle -->
                @if(Auth::user()->role === 'admin')
                <div x-data="{ paramsOpen: false }" class="space-y-1">
                    <button @click="paramsOpen = !paramsOpen"
                            class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs(['frais.*', 'frais-inscriptions.*', 'taux-reductions.*']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                        <span class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ __('Paramètres') }}</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': paramsOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="paramsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-40" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 max-h-40" x-transition:leave-end="opacity-0 max-h-0" class="ml-6 space-y-1 overflow-hidden">
                        <a href="{{ route('frais.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('frais.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-sm">{{ __('Frais') }}</span>
                        </a>
                        <a href="{{ route('frais-inscriptions.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('frais-inscriptions.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 8h.01M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                            </svg>
                            <span class="text-sm">{{ __("Frais d'inscription") }}</span>
                        </a>
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('taux-reductions.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('taux-reductions.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="text-sm">{{ __('Taux Réduction') }}</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Gestion Utilisateurs - Admin seulement -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('user-management.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 {{ request()->routeIs('user-management.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span class="font-medium text-sm">{{ __('Gestion Utilisateurs') }}</span>
                </a>
                @endif
                @endauth

                <!-- Mobile User Section -->
                @auth
                <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                    <div class="flex items-center space-x-3 px-4 py-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                            <div class="text-xs font-medium inline-flex items-center space-x-1 mt-1
                                @if(Auth::user()->role === 'admin') text-red-600 dark:text-red-400
                                @elseif(Auth::user()->role === 'professional') text-purple-600 dark:text-purple-400
                                @else text-blue-600 dark:text-blue-400 @endif">
                                <span>@if(Auth::user()->role === 'admin') 👑 Administrateur
                                     @elseif(Auth::user()->role === 'professional') 🔧 Professionnel
                                     @else 👤 Utilisateur @endif</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm">{{ __('Profile') }}</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span class="text-sm">{{ __('Log Out') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <!-- Mobile Guest Section -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-3 space-y-2">
                    <a href="{{ route('login') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ __('Connexion') }}</span>
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-green-600 text-white hover:from-blue-700 hover:to-green-700 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ __('Inscription') }}</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
