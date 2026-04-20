{{-- Sidebar Component - Rétractable --}}

{{-- Overlay --}}
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40">
</div>

{{-- Sidebar Panel --}}
<aside x-show="sidebarOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed left-0 top-0 h-screen w-72 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white shadow-2xl z-50 flex flex-col transform"
>
    <style>
        .sidebar-link {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, #3b82f6, #10b981);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover::before,
        .sidebar-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-link:hover {
            background: rgba(59, 130, 246, 0.15);
        }

        .sidebar-link.active {
            background: rgba(59, 130, 246, 0.2);
        }

        .sidebar-icon {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover .sidebar-icon {
            transform: scale(1.1);
            color: #3b82f6;
        }

        .submenu-enter {
            animation: slideDown 0.3s ease forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{-- Header avec bouton fermer --}}
    <div class="p-4 border-b border-slate-700/50 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3" @click="sidebarOpen = false">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">
                    MedConnect
                </h1>
                <p class="text-xs text-slate-400">Menu rapide</p>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
           class="sidebar-link flex items-center px-3 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-slate-700/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <span class="ml-3 font-medium">Tableau de bord</span>
        </a>

        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'professional')
        {{-- Gestion Client --}}
        <div x-data="{ open: {{ request()->routeIs(['dossier-medicals.*', 'subscriptions.*', 'controle-client.*', 'carte-medicale.*']) ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sidebar-link w-full flex items-center justify-between px-3 py-3 rounded-xl {{ request()->routeIs(['dossier-medicals.*', 'subscriptions.*', 'controle-client.*', 'carte-medicale.*']) ? 'active' : '' }}">
                <div class="flex items-center">
                    <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-blue-500/20">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">Gestion Client</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition:enter="submenu-enter" class="mt-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-4">
                <a href="{{ route('dossier-medicals.create') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('dossier-medicals.create') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouveau Dossier
                </a>
                <a href="{{ route('subscriptions.create') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('subscriptions.create') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réabonnement
                </a>
                <a href="{{ route('dossier-medicals.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('dossier-medicals.index') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Liste Dossiers
                </a>
                <a href="{{ route('dossier-medicals.pending-validation') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('dossier-medicals.pending-validation') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                    </svg>
                    Dossiers en attente
                </a>
                <a href="{{ route('controle-client.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('controle-client.*') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Contrôle Client
                </a>
                <a href="{{ route('carte-medicale.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('carte-medicale.*') ? 'text-blue-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    Carte Médicale
                </a>
            </div>
        </div>
        @endif

        @if(Auth::user()->role === 'admin')
        {{-- Paramètres --}}
        <div x-data="{ open: {{ request()->routeIs(['frais.*', 'frais-inscriptions.*', 'taux-reductions.*', 'services-medicaux.*', 'demandes-services.*']) ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sidebar-link w-full flex items-center justify-between px-3 py-3 rounded-xl {{ request()->routeIs(['frais.*', 'frais-inscriptions.*', 'taux-reductions.*', 'services-medicaux.*', 'demandes-services.*']) ? 'active' : '' }}">
                <div class="flex items-center">
                    <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-amber-500/20">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">Paramètres</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition:enter="submenu-enter" class="mt-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-4">
                <a href="{{ route('frais.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('frais.*') ? 'text-amber-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Frais
                </a>
                <a href="{{ route('services-medicaux.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('services-medicaux.*') ? 'text-amber-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Services
                </a>
                <a href="{{ route('demandes-services.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('demandes-services.*') ? 'text-amber-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Demandes Services
                </a>
                <a href="{{ route('frais-inscriptions.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('frais-inscriptions.*') ? 'text-amber-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Frais d'inscription
                </a>
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('taux-reductions.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('taux-reductions.*') ? 'text-amber-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Taux de réduction
                </a>
                @endif
            </div>
        </div>
        @endif

        @if(Auth::user()->role === 'admin')
        {{-- Gestion Utilisateurs --}}
        <a href="{{ route('user-management.index') }}" @click="sidebarOpen = false"
           class="sidebar-link flex items-center px-3 py-3 rounded-xl {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
            <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-purple-500/20">
                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="ml-3 font-medium">Utilisateurs</span>
        </a>

        {{-- Services Medical --}}
        <div x-data="{ servicesOpen: {{ request()->routeIs(['services-medicaux.*', 'demandes-services.*']) ? 'true' : 'false' }} }">
            <button @click="servicesOpen = !servicesOpen"
                    class="sidebar-link w-full flex items-center justify-between px-3 py-3 rounded-xl {{ request()->routeIs(['services-medicaux.*', 'demandes-services.*']) ? 'active' : '' }}">
                <div class="flex items-center">
                    <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-cyan-500/20">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">Services</span>
                </div>
                <svg :class="servicesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="servicesOpen" x-transition:enter="submenu-enter" class="mt-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-4">
                <a href="{{ route('services-medicaux.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('services-medicaux.*') ? 'text-cyan-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Services
                </a>
                <a href="{{ route('demandes-services.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('demandes-services.*') ? 'text-cyan-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Demandes
                </a>
            </div>
        </div>

        <a href="{{ route('dossier-professionnels.pending-validation') }}" @click="sidebarOpen = false"
           class="sidebar-link flex items-center px-3 py-3 rounded-xl {{ request()->routeIs('dossier-professionnels.pending-validation') ? 'active' : '' }}">
            <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-indigo-500/20">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
            </div>
            <span class="ml-3 font-medium">Pros en attente</span>
        </a>

        {{-- Rapports IA --}}
        <a href="{{ route('admin.optimization-reports.index') }}" @click="sidebarOpen = false"
           class="sidebar-link flex items-center px-3 py-3 rounded-xl {{ request()->routeIs('admin.optimization-reports.*') ? 'active' : '' }}">
            <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-cyan-500/20">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-3m4 6H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="ml-3 font-medium">Rapports IA</span>
        </a>

        <a href="{{ route('admin.soumissions-mutuelle.index') }}" @click="sidebarOpen = false"
           class="sidebar-link flex items-center px-3 py-3 rounded-xl {{ request()->routeIs('admin.soumissions-mutuelle.*') ? 'active' : '' }}">
            <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-amber-500/20">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="ml-3 font-medium">Prises en charge</span>
        </a>

        {{-- Finances --}}
        <div x-data="{ open: {{ request()->routeIs(['transactions.*', 'paiements.*']) ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sidebar-link w-full flex items-center justify-between px-3 py-3 rounded-xl {{ request()->routeIs(['transactions.*', 'paiements.*']) ? 'active' : '' }}">
                <div class="flex items-center">
                    <div class="sidebar-icon w-10 h-10 flex items-center justify-center rounded-lg bg-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">Finances</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition:enter="submenu-enter" class="mt-2 ml-4 space-y-1 border-l-2 border-slate-700 pl-4">
                <a href="{{ route('transactions.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('transactions.*') ? 'text-emerald-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Historique Transactions
                </a>
                <a href="{{ route('paiements.index') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors {{ request()->routeIs('paiements.*') ? 'text-emerald-400 bg-slate-700/50' : '' }}">
                    <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Paiements
                </a>
            </div>
        </div>
        @endif
    </nav>

    {{-- User Section --}}
    <div class="p-4 border-t border-slate-700/50">
        <div class="flex items-center p-2 rounded-xl bg-slate-700/30">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-xl flex items-center justify-center text-white font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="ml-3 flex-1">
                <p class="font-medium text-sm truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400">
                    @if(Auth::user()->role === 'admin')
                        <span class="text-red-400">👑 Admin</span>
                    @elseif(Auth::user()->role === 'professional')
                        <span class="text-purple-400">🔧 Pro</span>
                    @else
                        <span class="text-blue-400">👤 User</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-3 space-y-1">
            <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false" class="flex items-center px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mon Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</aside>
