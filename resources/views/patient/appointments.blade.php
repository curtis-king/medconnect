<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mes rendez-vous calendrier</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivi calendrier et liste detaillee de vos rendez-vous patient.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                <a href="{{ route('rendez-vous.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">Nouveau rendez-vous</a>
            </div>

            <div class="rounded-2xl border border-violet-200 dark:border-violet-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <button type="button" id="prevMonth" class="px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-xs">Mois precedent</button>
                    <h3 id="calendarTitle" class="text-sm font-semibold text-gray-900 dark:text-gray-100"></h3>
                    <button type="button" id="nextMonth" class="px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-xs">Mois suivant</button>
                </div>
                <div id="calendarGrid" class="grid grid-cols-7 gap-1 text-center text-xs"></div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Liste des rendez-vous</h3>
                <div class="mt-3 space-y-3">
                    @forelse($rendezVous as $rdv)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $rdv->reference }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Date: {{ optional($rdv->date_proposee)->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">Statut: {{ ucfirst((string) $rdv->statut) }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">Service: {{ $rdv->serviceProfessionnel?->nom ?? 'Service non defini' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">Professionnel: {{ $rdv->professionnel?->name ?? $rdv->dossierProfessionnel?->user?->name ?? 'Professionnel' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun rendez-vous.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const events = @json($calendarEvents);
            const titleElement = document.getElementById('calendarTitle');
            const gridElement = document.getElementById('calendarGrid');
            const prevButton = document.getElementById('prevMonth');
            const nextButton = document.getElementById('nextMonth');

            if (!titleElement || !gridElement || !prevButton || !nextButton) {
                return;
            }

            const monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
            const dayLabels = ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'];
            let currentDate = new Date();

            const formatDateKey = function (year, month, day) {
                const mm = String(month + 1).padStart(2, '0');
                const dd = String(day).padStart(2, '0');
                return year + '-' + mm + '-' + dd;
            };

            const renderCalendar = function () {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);

                let startOffset = firstDay.getDay() - 1;
                if (startOffset < 0) {
                    startOffset = 6;
                }

                titleElement.textContent = monthNames[month] + ' ' + year;
                gridElement.innerHTML = '';

                dayLabels.forEach(function (label) {
                    const headerCell = document.createElement('div');
                    headerCell.className = 'font-semibold text-gray-500 dark:text-gray-400 py-1';
                    headerCell.textContent = label;
                    gridElement.appendChild(headerCell);
                });

                for (let i = 0; i < startOffset; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'py-2';
                    gridElement.appendChild(emptyCell);
                }

                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const dateKey = formatDateKey(year, month, day);
                    const hasEvent = events.some(function (eventItem) {
                        return eventItem.date === dateKey;
                    });

                    const dayCell = document.createElement('div');
                    dayCell.className = 'py-2 rounded-lg border text-gray-700 dark:text-gray-200 ' + (hasEvent ? 'bg-violet-100 dark:bg-violet-900/40 border-violet-300 dark:border-violet-700 font-semibold' : 'border-gray-100 dark:border-gray-700');
                    dayCell.textContent = String(day);
                    gridElement.appendChild(dayCell);
                }
            };

            prevButton.addEventListener('click', function () {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
                renderCalendar();
            });

            nextButton.addEventListener('click', function () {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                renderCalendar();
            });

            renderCalendar();
        });
    </script>
@endpush
