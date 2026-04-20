<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Page 1 - Patient presentiel</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Identification par carte/numero dossier et ouverture directe de la consultation.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Accueil physique patient</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Le medecin saisit la carte ou numero dossier, puis le systeme ouvre la consultation editable (examiner, ordonnance, examens, impression).</p>

                <div class="mt-4 rounded-xl border border-cyan-200 dark:border-cyan-700 bg-cyan-50/70 dark:bg-cyan-900/20 p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-cyan-900 dark:text-cyan-100">Scan QR carte medicale</p>
                            <p class="text-xs text-cyan-700/90 dark:text-cyan-300/90">Demarrage automatique du lecteur QR a l'ouverture de la page (PC/mobile). Si bloque par le navigateur, cliquez sur Demarrer.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="startScanBtn" class="px-3 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-medium">Demarrer le scan</button>
                            <button type="button" id="stopScanBtn" class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-xs font-medium">Arreter</button>
                        </div>
                    </div>

                    <div id="scanContainer" class="mt-3 hidden">
                        <div id="qrReader" class="w-full md:w-[420px] max-w-full rounded-lg border border-cyan-300 dark:border-cyan-700 bg-black overflow-hidden"></div>
                        <p id="scanStatus" class="mt-2 text-xs text-cyan-800 dark:text-cyan-200">Initialisation camera...</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('professional.workspace.presentiel.start') }}" class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero carte / dossier</label>
                        <input type="text" id="numeroDossierInput" name="numero_dossier" value="{{ old('numero_dossier') }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                               placeholder="Ex: DOS-000245 ou ID dossier">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service presentiel</label>
                        <select name="service_professionnel_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">-- Selectionner --</option>
                            @foreach($servicesConsultation as $service)
                                <option value="{{ $service->id }}" @selected((int) old('service_professionnel_id') === (int) $service->id)>
                                    {{ $service->nom }} - {{ number_format((float) $service->prix, 0, ',', ' ') }} XAF
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif (optionnel)</label>
                        <textarea name="motif" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                  placeholder="Motif rapide de la visite">{{ old('motif') }}</textarea>
                    </div>

                    <div class="md:col-span-2 flex items-center justify-between">
                        <a href="{{ route('professional.workspace.dashboard') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retour dashboard</a>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium">Identifier et ouvrir la consultation</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Dernieres consultations presencieles</h3>
                </div>
                <div class="p-5">
                    @if($consultationsRecentes->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune consultation recente.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">Patient</th>
                                        <th class="py-2 pr-4">Dossier</th>
                                        <th class="py-2 pr-4">Date</th>
                                        <th class="py-2 pr-4">Facture</th>
                                        <th class="py-2 pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consultationsRecentes as $consultation)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-3 pr-4 text-gray-900 dark:text-gray-100">{{ $consultation->patient?->name ?? (($consultation->dossierMedical?->prenom ?? '') . ' ' . ($consultation->dossierMedical?->nom ?? '')) }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $consultation->numero_dossier_reference ?? $consultation->dossierMedical?->numero_unique ?? '—' }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ optional($consultation->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $consultation->factures->first()?->reference ?? '—' }}</td>
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('professional.workspace.consultation.edit', $consultation) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Ouvrir consultation-edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startButton = document.getElementById('startScanBtn');
            const stopButton = document.getElementById('stopScanBtn');
            const container = document.getElementById('scanContainer');
            const qrReader = document.getElementById('qrReader');
            const status = document.getElementById('scanStatus');
            const numeroInput = document.getElementById('numeroDossierInput');

            if (!startButton || !stopButton || !container || !qrReader || !status || !numeroInput) {
                return;
            }

            let scanner = null;
            let scannerStarted = false;

            const secureContext = window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1';

            if (!secureContext) {
                status.textContent = 'Camera indisponible: utilisez HTTPS ou localhost pour autoriser le scan.';
            }

            const normalizeScanCode = function (value) {
                const raw = String(value || '').trim();

                if (!raw) {
                    return '';
                }

                if (/^https?:\/\//i.test(raw)) {
                    try {
                        const url = new URL(raw);
                        const pathParts = url.pathname.split('/').filter(Boolean);

                        if (pathParts.length > 0) {
                            return decodeURIComponent(pathParts[pathParts.length - 1]);
                        }
                    } catch (error) {
                        return raw;
                    }
                const ensureScanner = function () {
                    if (!window.Html5Qrcode) {
                        return null;

            const stopScan = function () {
                    if (!scanner) {
                        scanner = new window.Html5Qrcode('qrReader');
                if (mediaStream) {

                    return scanner;
                };

                const stopScan = async function () {
                    if (scanner && scannerStarted) {
                        try {
                            await scanner.stop();
                            await scanner.clear();
                        } catch (error) {
                            // no-op
                        }
                    }

                    scannerStarted = false;
                    container.classList.add('hidden');
                    status.textContent = 'Scan arrete.';
                };

                const onScanSuccess = async function (decodedText) {
                    const scannedValue = normalizeScanCode(decodedText);
                    if (!scannedValue) {
                        return;
                    }
                    mediaStream.getTracks().forEach(function (track) {
                    numeroInput.value = scannedValue;
                    status.textContent = 'Code detecte: ' + scannedValue;
                    await stopScan();
                };

                const startScan = async function (isAutoStart) {
                    });
                    const scanEngine = ensureScanner();

                    if (!scanEngine) {
                        status.textContent = 'Lecteur QR indisponible. Verifiez la connexion internet.';
                status.textContent = 'Scan arrete.';
            };

            const runDetection = async function () {
                        const cameras = await window.Html5Qrcode.getCameras();
                        if (!cameras || !cameras.length) {
                            status.textContent = 'Aucune camera detectee sur cet appareil.';
                            return;
                        }

                        const preferredCamera = cameras.find(function (camera) {
                            return /back|rear|environment/gi.test(camera.label || '');
                        }) || cameras[0];

                        const config = {
                            fps: 10,
                            qrbox: { width: 260, height: 260 },
                            aspectRatio: 1.333334,
                        };

                        await scanEngine.start(
                            { deviceId: { exact: preferredCamera.id } },
                            config,
                            onScanSuccess,
                            function () {
                                // ignore frame errors during scan
                            }
                        );

                        scannerStarted = true;
                        status.textContent = isAutoStart
                            ? 'Camera active automatiquement. Placez le QR code devant la camera.'
                            : 'Camera active. Placez le QR code devant la camera.';
                    } catch (error) {
                        scannerStarted = false;
                        status.textContent = isAutoStart
                            ? 'Demarrage auto bloque par le navigateur. Cliquez sur Demarrer le scan.'
                            : 'Acces camera refuse ou indisponible. Verifiez les permissions navigateur.';
                    }
                };
                    return;
                startButton.addEventListener('click', async function () {
                    await startScan(false);
                });
                    const barcodes = await barcodeDetector.detect(video);
                stopButton.addEventListener('click', async function () {
                    await stopScan();
                });

                if (secureContext) {
                    startScan(true);
                }
                    stopScan();
                window.addEventListener('beforeunload', function () {
                    stopScan();
                }
            });

            stopButton.addEventListener('click', function () {
                stopScan();
            });
        });
    </script>
@endpush
