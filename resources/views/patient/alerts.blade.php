<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Alertes sante et notifications</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivi des alertes medicales et de toutes les actions du parcours patient.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <form method="POST" action="{{ route('patient.alerts.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-medium text-gray-700 dark:text-gray-100">Tout marquer lu</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-amber-200 dark:border-amber-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300">Notifications non lues</p>
                    <p data-unread-counter class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $unreadCount }}</p>
                </div>
                <div class="rounded-2xl border border-red-200 dark:border-red-700 bg-white dark:bg-gray-800 p-4 shadow-sm md:col-span-2">
                    <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300">Voyant activite</p>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Un point rouge apparait sur les notifications non lues (acceptation rendez-vous, resultat disponible, ordonnance, facture, etc.).</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Alertes sante</h3>
                <div class="mt-3 space-y-2">
                    @forelse($alerts as $alert)
                        <div class="rounded-xl border p-3 {{ $alert['niveau'] === 'critical' ? 'border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20' : ($alert['niveau'] === 'warning' ? 'border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/20' : 'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20') }}">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $alert['titre'] }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $alert['message'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune alerte sante en ce moment.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications du systeme</h3>
                <div id="patient-notification-list" class="mt-3 space-y-2">
                    @forelse($notifications as $notification)
                        <div data-notification-id="{{ $notification->id }}" class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    @if($notification->read_at === null)
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                    @endif
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Type: {{ $notification->data['type'] ?? 'information' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($notification->created_at)->format('d/m/Y H:i') }}</p>
                            </div>

                            @if($notification->read_at === null)
                                <form method="POST" action="{{ route('patient.alerts.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium">Marquer lue</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p id="patient-notification-empty" class="text-sm text-gray-500 dark:text-gray-400">Aucune notification disponible.</p>
                    @endforelse
                </div>
                <div class="mt-4">{{ $notifications->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const list = document.getElementById('patient-notification-list');

            if (!list) {
                return;
            }

            const renderNotification = (payload) => {
                const card = document.createElement('div');
                card.className = 'rounded-xl border border-gray-200 dark:border-gray-700 p-3 flex items-start justify-between gap-3';
                if (payload?.id) {
                    card.setAttribute('data-notification-id', String(payload.id));
                }

                const message = payload?.message ?? 'Nouvelle notification';
                const type = payload?.type ?? 'information';
                const createdAt = new Date();

                card.innerHTML = `
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100"></p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                `;

                const messageNode = card.querySelector('p.text-sm');
                const typeNode = card.querySelectorAll('p.text-xs')[0];
                const dateNode = card.querySelectorAll('p.text-xs')[1];

                if (messageNode) {
                    messageNode.textContent = message;
                }

                if (typeNode) {
                    typeNode.textContent = `Type: ${type}`;
                }

                if (dateNode) {
                    dateNode.textContent = createdAt.toLocaleString('fr-FR');
                }

                return card;
            };

            document.addEventListener('medconnect:notification-received', function (event) {
                const payload = event?.detail ?? {};
                const empty = document.getElementById('patient-notification-empty');

                 if (payload?.id && list.querySelector(`[data-notification-id="${payload.id}"]`)) {
                    return;
                }

                if (empty) {
                    empty.remove();
                }

                list.prepend(renderNotification(payload));
            });
        });
    </script>
@endpush
