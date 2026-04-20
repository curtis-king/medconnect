<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MEDCONNECT') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('medconnect_3.png') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .main-content-with-sidebar {
                transition: margin-left 0.3s ease;
            }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true' }">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @auth
                @php
                    $currentUser = auth()->user();
                    $isRoleShell = in_array($currentUser->role, ['user', 'professional'], true);
                    $isProfessionalShell = $currentUser->role === 'professional';
                @endphp
            @endauth

            @auth
                @if($isRoleShell)
                    <div x-data="{ shellOpen: false }" class="min-h-screen bg-slate-50 dark:bg-slate-900">
                        <div
                            x-show="shellOpen"
                            x-transition.opacity
                            class="fixed inset-0 z-40 bg-slate-950/60 lg:hidden"
                            @click="shellOpen = false"
                        ></div>

                        <aside
                            class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full transform border-r border-slate-200 bg-white/95 backdrop-blur lg:translate-x-0 dark:border-slate-800 dark:bg-slate-950/95"
                            :class="shellOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="-translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="-translate-x-full"
                        >
                            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 dark:border-slate-800">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900">
                                        <x-application-logo class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ config('app.name', 'MEDCONNECT') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $isProfessionalShell ? 'Espace medecin' : 'Espace patient' }}</p>
                                    </div>
                                </a>
                                <button
                                    type="button"
                                    class="rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden dark:hover:bg-slate-800"
                                    @click="shellOpen = false"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <nav class="space-y-1 p-3">
                                @if($isProfessionalShell)
                                    @php
                                        $shellItems = [
                                            ['label' => 'Dashboard', 'route' => 'professional.workspace.dashboard', 'patterns' => ['professional.workspace.dashboard']],
                                            ['label' => 'Presentiel', 'route' => 'professional.workspace.presentiel', 'patterns' => ['professional.workspace.presentiel']],
                                            ['label' => 'Suivi patients', 'route' => 'professional.workspace.patients.tracking', 'patterns' => ['professional.workspace.patients.tracking']],
                                            ['label' => 'Finances', 'route' => 'professional.workspace.finance', 'patterns' => ['professional.workspace.finance*']],
                                            ['label' => 'Repertoire', 'route' => 'professional.workspace.patients.directory', 'patterns' => ['professional.workspace.patients.directory']],
                                            ['label' => 'Historique', 'route' => 'professional.workspace.patients.history', 'patterns' => ['professional.workspace.patients.history']],
                                        ];
                                    @endphp
                                @else
                                    @php
                                        $shellItems = [
                                            ['label' => 'Dashboard', 'route' => 'dashboard', 'patterns' => ['dashboard']],
                                            ['label' => 'Paiements', 'route' => 'patient.payments.index', 'patterns' => ['patient.payments.*']],
                                            ['label' => 'Finances', 'route' => 'patient.finances.index', 'patterns' => ['patient.finances.*']],
                                            ['label' => 'Rendez-vous', 'route' => 'patient.appointments.index', 'patterns' => ['patient.appointments.*', 'rendez-vous.*']],
                                            ['label' => 'Documents', 'route' => 'patient.documents.index', 'patterns' => ['patient.documents.*']],
                                            ['label' => 'Alertes', 'route' => 'patient.alerts.index', 'patterns' => ['patient.alerts.*']],
                                        ];
                                    @endphp
                                @endif

                                @foreach($shellItems as $shellItem)
                                    @php
                                        $isActive = false;
                                        foreach ($shellItem['patterns'] as $pattern) {
                                            if (request()->routeIs($pattern)) {
                                                $isActive = true;
                                                break;
                                            }
                                        }
                                    @endphp

                                    <a
                                        href="{{ route($shellItem['route']) }}"
                                        class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"
                                        @click="shellOpen = false"
                                    >
                                        <span>{{ $shellItem['label'] }}</span>
                                        @if($shellItem['route'] === 'patient.alerts.index')
                                            <span data-unread-indicator class="{{ ($currentUser->unreadNotifications()->count() ?? 0) > 0 ? '' : 'hidden' }} inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </nav>

                            <div class="mt-auto border-t border-slate-200 p-3 dark:border-slate-800">
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="mb-2 flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                                    @click="shellOpen = false"
                                >
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-950/40">
                                        Deconnexion
                                    </button>
                                </form>
                            </div>
                        </aside>

                        <div class="lg:pl-72">
                            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
                                <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <button
                                            type="button"
                                            class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                            @click="shellOpen = true"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>
                                        <div>
                                            @isset($header)
                                                {{ $header }}
                                            @else
                                                <h1 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $isProfessionalShell ? 'Dashboard medecin' : 'Dashboard patient' }}</h1>
                                            @endisset
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="hidden text-right sm:block">
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $currentUser->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $currentUser->email }}</p>
                                        </div>
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <main>
                                {{ $slot }}
                            </main>
                        </div>
                    </div>
                @else
                    @include('layouts.navigation')

                    @include('layouts.sidebar')

                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main>
                        {{ $slot }}
                    </main>
                @endif
            @else
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            @endauth
        </div>

        @stack('scripts')

        @auth
            <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.__medconnectGlobalRealtimeReady) {
                        return;
                    }

                    const userId = @json((int) auth()->id());
                    const reverbKey = @json(config('broadcasting.connections.reverb.key'));
                    const reverbHost = @json(config('broadcasting.connections.reverb.options.host'));
                    const reverbPort = Number(@json(config('broadcasting.connections.reverb.options.port')));
                    const reverbScheme = @json(config('broadcasting.connections.reverb.options.scheme'));
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
                    const liveNotificationsUrl = @json(route('patient.notifications.live'));
                    const knownNotificationIds = new Set();
                    let pollingStarted = false;

                    window.__medconnectGlobalRealtimeReady = true;

                    const setUnreadIndicators = (count) => {
                        const nextCount = Number(count) || 0;

                        document.querySelectorAll('[data-unread-counter]').forEach((element) => {
                            element.textContent = String(nextCount);
                        });

                        document.querySelectorAll('[data-unread-indicator]').forEach((element) => {
                            element.classList.toggle('hidden', nextCount <= 0);
                        });
                    };

                    const increaseUnreadIndicators = () => {
                        const firstCounter = document.querySelector('[data-unread-counter]');
                        const current = Number(firstCounter?.textContent ?? 0) || 0;
                        setUnreadIndicators(current + 1);
                    };

                    const dispatchNotification = (payload) => {
                        document.dispatchEvent(new CustomEvent('medconnect:notification-received', {
                            detail: payload ?? {},
                        }));
                    };

                    const startPollingFallback = () => {
                        if (pollingStarted || !liveNotificationsUrl) {
                            return;
                        }

                        pollingStarted = true;
                        let initialized = false;

                        const syncNotifications = async () => {
                            try {
                                const response = await window.axios.get(liveNotificationsUrl);
                                const payload = response?.data ?? {};
                                const notifications = Array.isArray(payload.notifications) ? payload.notifications : [];

                                setUnreadIndicators(payload.unread_count ?? 0);

                                notifications.forEach((notification) => {
                                    if (notification?.id) {
                                        if (!initialized) {
                                            knownNotificationIds.add(notification.id);
                                            return;
                                        }

                                        if (knownNotificationIds.has(notification.id)) {
                                            return;
                                        }

                                        knownNotificationIds.add(notification.id);
                                    }

                                    dispatchNotification({
                                        ...(notification?.data ?? {}),
                                        id: notification?.id,
                                        message: notification?.message,
                                        type: notification?.type,
                                        created_at: notification?.created_at,
                                        read_at: notification?.read_at,
                                    });
                                });

                                initialized = true;
                            } catch (error) {
                                console.debug('Fallback polling notifications indisponible.', error);
                            }
                        };

                        syncNotifications();
                        window.setInterval(syncNotifications, 6000);
                    };

                    if (!window.Pusher || !userId || !reverbKey || !reverbHost) {
                        startPollingFallback();
                        return;
                    }

                    const pusher = new window.Pusher(reverbKey, {
                        wsHost: reverbHost,
                        wsPort: reverbPort,
                        wssPort: reverbPort,
                        forceTLS: reverbScheme === 'https',
                        enabledTransports: ['ws', 'wss'],
                        authEndpoint: '/broadcasting/auth',
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        },
                    });

                    const userNotificationChannel = pusher.subscribe(`private-App.Models.User.${userId}`);
                    userNotificationChannel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function (payload) {
                        increaseUnreadIndicators();
                        dispatchNotification(payload ?? {});
                    });

                    pusher.connection.bind('error', function () {
                        startPollingFallback();
                    });

                    pusher.connection.bind('disconnected', function () {
                        startPollingFallback();
                    });

                    window.medconnectSetUnreadIndicators = setUnreadIndicators;
                });
            </script>
        @endauth
    </body>
</html>
