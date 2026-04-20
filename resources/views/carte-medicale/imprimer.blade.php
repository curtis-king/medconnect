<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Médicale - {{ $dossier->prenom }} {{ $dossier->nom }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #d1fae5 100%);
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            min-height: 100vh;
            padding: 30px;
        }

        @media print {
            body {
                background: white !important;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .cards-wrapper {
                gap: 20px !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }

        .print-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .header-controls {
            background: white;
            padding: 20px 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.8);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .btn-print {
            background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }

        .btn-print:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.5);
        }

        .btn-back {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            transform: translateY(-2px);
        }

        .cards-wrapper {
            display: flex;
            flex-direction: column;
            gap: 40px;
            align-items: center;
        }

        .card-section {
            text-align: center;
        }

        .card-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .card-label .badge {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .card {
            width: 400px;
            height: 252px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.1);
            position: relative;
            transform: perspective(1000px) rotateX(2deg);
            transition: all 0.4s ease;
        }

        .card:hover {
            transform: perspective(1000px) rotateX(0deg) scale(1.02);
            box-shadow: 0 35px 70px rgba(0,0,0,0.2);
        }

        /* RECTO - Design moderne bleu-vert */
        .card-front {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 25%, #10b981 50%, #059669 75%, #047857 100%);
            color: white;
            padding: 24px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .card-front::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-front::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Motif géométrique */
        .pattern-overlay {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            background-image:
                radial-gradient(circle at 20% 80%, white 1px, transparent 1px),
                radial-gradient(circle at 80% 20%, white 1px, transparent 1px),
                radial-gradient(circle at 40% 40%, white 2px, transparent 2px);
            background-size: 60px 60px, 80px 80px, 100px 100px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .logo-icon svg {
            width: 26px;
            height: 26px;
            fill: white;
        }

        .logo-text {
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .logo-subtitle {
            font-size: 10px;
            opacity: 0.8;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .blood-type {
            background: white;
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .card-body {
            display: flex;
            gap: 20px;
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .photo-section {
            flex-shrink: 0;
            position: relative;
        }

        .photo-glow {
            position: absolute;
            inset: -4px;
            background: linear-gradient(135deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1));
            border-radius: 18px;
            filter: blur(4px);
        }

        .photo {
            position: relative;
            width: 64px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .photo-placeholder {
            position: relative;
            width: 64px;
            height: 80px;
            border-radius: 12px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            border: 2px solid rgba(255,255,255,0.5);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-label {
            font-size: 9px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }

        .name-surname {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .name-first {
            font-weight: 600;
            font-size: 17px;
            opacity: 0.95;
            margin-top: 2px;
        }

        .info-badges {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .info-badge {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 12px;
            margin-top: auto;
            position: relative;
            z-index: 2;
        }

        .card-number-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .card-number-label {
            font-size: 8px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-number {
            font-family: 'SF Mono', 'Consolas', monospace;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .status-valid {
            background: rgba(52, 211, 153, 0.3);
            border: 1px solid rgba(52, 211, 153, 0.5);
        }

        .status-expired {
            background: rgba(251, 191, 36, 0.3);
            border: 1px solid rgba(251, 191, 36, 0.5);
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            box-shadow: 0 0 10px currentColor;
        }

        .status-valid .status-dot {
            background: #34d399;
            animation: pulse 2s infinite;
        }

        .status-expired .status-dot {
            background: #fbbf24;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(0.95); }
        }

        /* VERSO */
        .card-back {
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%);
            color: #1e293b;
            position: relative;
        }

        .magnetic-strip {
            height: 44px;
            background: linear-gradient(90deg, #1e293b 0%, #334155 30%, #475569 50%, #334155 70%, #1e293b 100%);
            margin-top: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .hologram-line {
            height: 3px;
            background: linear-gradient(90deg,
                transparent 0%,
                #0ea5e9 20%,
                #10b981 40%,
                #06b6d4 60%,
                #059669 80%,
                transparent 100%);
            margin: 12px 20px 0;
            border-radius: 2px;
            opacity: 0.7;
        }

        .back-content {
            padding: 12px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .medical-card {
            background: white;
            border-radius: 12px;
            padding: 10px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid;
        }

        .medical-card.allergy {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2, white);
        }

        .medical-card.disease {
            border-color: #f97316;
            background: linear-gradient(135deg, #fff7ed, white);
        }

        .medical-card.treatment {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff, white);
        }

        .medical-card.emergency {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5, white);
            grid-column: span 2;
        }

        .medical-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .medical-card.allergy .medical-title { color: #dc2626; }
        .medical-card.disease .medical-title { color: #ea580c; }
        .medical-card.treatment .medical-title { color: #2563eb; }
        .medical-card.emergency .medical-title { color: #059669; }

        .medical-value {
            font-size: 10px;
            color: #475569;
            line-height: 1.3;
        }

        .emergency-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: center;
        }

        .emergency-phone {
            font-family: 'SF Mono', monospace;
            font-weight: 700;
            font-size: 12px;
            color: #059669;
        }

        .qr-box {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        .qr-box svg {
            width: 38px;
            height: 38px;
            fill: #334155;
        }

        .back-footer {
            position: absolute;
            bottom: 12px;
            left: 20px;
            right: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
        }

        .expiry-info {
            color: #64748b;
        }

        .expiry-date {
            font-weight: 700;
            color: #059669;
        }

        .website {
            color: #94a3b8;
            font-family: 'SF Mono', monospace;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Contrôles -->
        <div class="header-controls no-print">
            <a href="{{ route('carte-medicale.generer', $dossier->id) }}" class="btn btn-back">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
            <button onclick="window.print()" class="btn btn-print">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimer
            </button>
        </div>

        <div class="cards-wrapper">
            <!-- RECTO -->
            <div class="card-section">
                <div class="card-label no-print">
                    <span class="badge">1</span>
                    Recto (Face avant)
                </div>
                <div class="card card-front">
                    <div class="pattern-overlay"></div>

                    <div class="card-header">
                        <div class="logo-section">
                            <div class="logo-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="logo-text">MEDCONNECT</div>
                                <div class="logo-subtitle">Carte Santé Officielle</div>
                            </div>
                        </div>
                        @if($dossier->groupe_sanguin)
                        <div class="blood-type">
                            <span>{{ $dossier->groupe_sanguin }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="photo-section">
                            <div class="photo-glow"></div>
                            @if($dossier->photo_profil_path)
                                <img src="{{ asset('storage/' . $dossier->photo_profil_path) }}" alt="Photo" class="photo">
                            @else
                                <div class="photo-placeholder">
                                    {{ strtoupper(substr($dossier->prenom, 0, 1) . substr($dossier->nom, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="info-section">
                            <div class="info-label">Nom & Prénom</div>
                            <div class="name-surname">{{ strtoupper($dossier->nom) }}</div>
                            <div class="name-first">{{ $dossier->prenom }}</div>

                            <div class="info-badges">
                                <div class="info-badge">
                                    <span>{{ $dossier->sexe === 'Masculin' ? 'Homme' : 'Femme' }}</span>
                                </div>
                                <div class="info-badge">
                                    <span>{{ $dossier->date_naissance?->format('d/m/Y') ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="card-number-box">
                            <div class="card-number-label">N° Carte</div>
                            <div class="card-number">{{ $dossier->numero_unique }}</div>
                        </div>
                        @if($dossier->activeSubscription)
                            <div class="status-badge status-valid">
                                <span class="status-dot"></span>
                                <span>ACTIVE</span>
                            </div>
                        @else
                            <div class="status-badge status-expired">
                                <span class="status-dot"></span>
                                <span>EXPIRÉE</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- VERSO -->
            <div class="card-section">
                <div class="card-label no-print">
                    <span class="badge">2</span>
                    Verso (Face arrière)
                </div>
                <div class="card card-back">
                    <div class="magnetic-strip"></div>
                    <div class="hologram-line"></div>

                    <div class="back-content">
                        <div class="medical-card emergency" style="grid-column: span 1;">
                            <div class="medical-title">Contact d'urgence</div>
                            <div>
                                @if($dossier->contact_urgence_nom)
                                    <div class="medical-value">{{ $dossier->contact_urgence_nom }}</div>
                                    <div class="emergency-phone">{{ $dossier->contact_urgence_telephone ?? 'N/A' }}</div>
                                    @if($dossier->contact_urgence_relation)
                                    <div class="medical-value" style="margin-top: 4px;">{{ $dossier->contact_urgence_relation }}</div>
                                    @endif
                                @else
                                    <div class="medical-value" style="color: #94a3b8; font-style: italic;">Non renseigné</div>
                                @endif
                            </div>
                        </div>

                        @if($dossier->groupe_sanguin)
                        <div class="medical-card" style="border-color: #0ea5e9; background: linear-gradient(135deg, #f0f9ff, white);">
                            <div class="medical-title" style="color: #0369a1;">Groupe Sanguin</div>
                            <div class="medical-value" style="font-size: 18px; font-weight: 800; color: #dc2626;">{{ $dossier->groupe_sanguin }}</div>
                        </div>
                        @endif

                        <div style="grid-column: span 2; display: flex; justify-content: center; align-items: center; flex-direction: column; padding: 10px 0;">
                            <div id="qrcode-print" class="qr-box" style="width: 80px; height: 80px;">
                                <!-- QR Code généré par JS -->
                            </div>
                            <p style="font-size: 9px; color: #64748b; margin-top: 8px; text-align: center;">Scanner pour accéder aux<br>informations médicales</p>
                        </div>
                    </div>

                    <div class="back-footer">
                        <div class="expiry-info">
                            @if($dossier->activeSubscription)
                                Valide jusqu'au <span class="expiry-date">{{ $dossier->activeSubscription->date_fin->format('d/m/Y') }}</span>
                            @else
                                <span style="color: #d97706; font-weight: 600;">Abonnement expiré</span>
                            @endif
                        </div>
                        <div class="website">medconnect.cm</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrUrl = @js(route('carte-medicale.scan', ['code' => $dossier->code_partage ?? $dossier->numero_unique]));
            const qrContainer = document.getElementById('qrcode-print');

            if (!qrContainer) {
                return;
            }

            if (!window.QRCode || typeof window.QRCode.toCanvas !== 'function') {
                qrContainer.innerHTML = '<span style="font-size: 9px; color: #d97706; text-align: center;">QR indisponible</span>';
                return;
            }

            const canvas = document.createElement('canvas');

            window.QRCode.toCanvas(canvas, qrUrl, {
                width: 72,
                margin: 1,
                color: {
                    dark: '#1e293b',
                    light: '#ffffff'
                }
            }, function(error) {
                if (error) {
                    console.error('QR print generation failed:', error);
                    qrContainer.innerHTML = '<span style="font-size: 9px; color: #d97706; text-align: center;">QR indisponible</span>';
                    return;
                }

                qrContainer.innerHTML = '';
                qrContainer.appendChild(canvas);
            });
        });
    </script>
</body>
</html>
