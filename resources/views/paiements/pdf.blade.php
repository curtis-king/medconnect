<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - {{ $paiement->reference_paiement ?? 'REF-' . str_pad($paiement->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        /* En-tête avec logo */
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .logo svg {
            width: 40px;
            height: 40px;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 2px;
        }

        .receipt-info {
            text-align: right;
        }

        .receipt-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .receipt-number {
            font-size: 13px;
            opacity: 0.9;
            font-family: monospace;
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 4px;
        }

        /* Bande de statut */
        .status-strip {
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .status-strip.paye {
            background: linear-gradient(90deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .status-strip.en_attente {
            background: linear-gradient(90deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .status-strip.annule {
            background: linear-gradient(90deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-icon {
            width: 18px;
            height: 18px;
        }

        /* Contenu principal */
        .main {
            padding: 40px;
        }

        /* Section patient */
        .patient-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            gap: 25px;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .patient-avatar {
            flex-shrink: 0;
        }

        .patient-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #2563eb;
            object-fit: cover;
        }

        .patient-avatar .initials {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 700;
        }

        .patient-details h3 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .patient-details .dossier-num {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            font-family: monospace;
        }

        .patient-details .patient-info-row {
            margin-top: 12px;
            display: flex;
            gap: 25px;
            font-size: 13px;
            color: #64748b;
        }

        .patient-details .patient-info-row span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Grille d'informations */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
        }

        .info-card h4 {
            color: #2563eb;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card h4 svg {
            width: 16px;
            height: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: #64748b;
            font-size: 13px;
        }

        .info-row .value {
            color: #1e293b;
            font-weight: 600;
            font-size: 13px;
        }

        /* Montant */
        .amount-card {
            background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .amount-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(30deg);
        }

        .amount-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .amount-value {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -1px;
            position: relative;
        }

        .amount-currency {
            font-size: 24px;
            font-weight: 400;
            margin-left: 5px;
        }

        /* Footer */
        .receipt-footer {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 30px;
            padding-top: 25px;
            border-top: 2px dashed #e2e8f0;
            margin-top: 20px;
        }

        .footer-details h4 {
            color: #2563eb;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .footer-details p {
            color: #64748b;
            font-size: 13px;
            margin: 6px 0;
            line-height: 1.5;
        }

        .footer-details strong {
            color: #1e293b;
        }

        .qr-section {
            text-align: center;
        }

        .qr-section img {
            width: 120px;
            height: 120px;
            border: 3px solid #2563eb;
            border-radius: 8px;
            padding: 5px;
            background: white;
        }

        .qr-section p {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .line {
            border-top: 2px solid #1e293b;
            margin-bottom: 8px;
            height: 50px;
        }

        .signature-box p {
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
        }

        /* Notes */
        .notes-section {
            margin-top: 25px;
            padding: 15px 20px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
        }

        .notes-section h5 {
            color: #92400e;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notes-section p {
            color: #78716c;
            font-size: 13px;
            line-height: 1.6;
        }

        /* Pied de page document */
        .document-footer {
            background: #f8fafc;
            padding: 15px 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }

        /* Print button */
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #2563eb 0%, #10b981 100%);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(37, 99, 235, 0.5);
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .container {
                box-shadow: none;
                border-radius: 0;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimer le reçu
    </button>

    <div class="container">
        <!-- En-tête avec Logo -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="#2563eb"/>
                        <path d="M2 17L12 22L22 17" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-name">MedConnect</div>
                    <div class="brand-tagline">Gestion Médicale Professionnelle</div>
                </div>
            </div>
            <div class="receipt-info">
                <div class="receipt-title">Reçu de Paiement</div>
                <div class="receipt-number">{{ $paiement->reference_paiement ?? 'REF-' . str_pad($paiement->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <!-- Bande de statut -->
        <div class="status-strip {{ $paiement->statut }}">
            <div class="status-badge">
                @if($paiement->statut === 'paye')
                    <svg class="status-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Paiement Confirmé
                @elseif($paiement->statut === 'en_attente')
                    <svg class="status-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    En Attente de Paiement
                @else
                    <svg class="status-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ ucfirst(str_replace('_', ' ', $paiement->statut)) }}
                @endif
            </div>
            <div>
                Émis le {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>

        <!-- Contenu -->
        <div class="main">
            <!-- Carte Patient -->
            <div class="patient-card">
                <div class="patient-avatar">
                    @if($paiement->dossierMedical->photo_profil_path)
                        <img src="{{ asset('storage/' . $paiement->dossierMedical->photo_profil_path) }}" alt="Photo">
                    @else
                        <div class="initials">
                            {{ strtoupper(substr($paiement->dossierMedical->prenom, 0, 1) . substr($paiement->dossierMedical->nom, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="patient-details">
                    <h3>{{ $paiement->dossierMedical->nom_complet }}</h3>
                    <span class="dossier-num">{{ $paiement->dossierMedical->numero_unique }}</span>
                    <div class="patient-info-row">
                        <span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $paiement->dossierMedical->telephone ?? 'Non renseigné' }}
                        </span>
                        <span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if($paiement->dossierMedical->date_naissance)
                                {{ $paiement->dossierMedical->date_naissance->format('d/m/Y') }}
                            @else
                                Non renseignée
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Grille d'informations -->
            <div class="info-grid">
                <div class="info-card">
                    <h4>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Détails du Paiement
                    </h4>
                    <div class="info-row">
                        <span class="label">Type</span>
                        <span class="value">{{ ucfirst($paiement->type_paiement) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Mode</span>
                        <span class="value">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</span>
                    </div>
                    @if($paiement->fraisInscription)
                    <div class="info-row">
                        <span class="label">Formule</span>
                        <span class="value">{{ $paiement->fraisInscription->libelle }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="label">Durée</span>
                        <span class="value">{{ $paiement->nombre_mois }} mois</span>
                    </div>
                </div>

                <div class="info-card">
                    <h4>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Période de Validité
                    </h4>
                    <div class="info-row">
                        <span class="label">Date de début</span>
                        <span class="value">{{ $paiement->periode_debut ? $paiement->periode_debut->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Date de fin</span>
                        <span class="value">{{ $paiement->periode_fin ? $paiement->periode_fin->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Jours restants</span>
                        <span class="value">
                            @if($paiement->periode_fin && $paiement->periode_fin->isFuture())
                                {{ now()->diffInDays($paiement->periode_fin) }} jours
                            @elseif($paiement->periode_fin)
                                Expiré
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Montant -->
            <div class="amount-card">
                <div class="amount-label">Montant Total</div>
                <div class="amount-value">
                    {{ number_format($paiement->montant, 0, ',', ' ') }}<span class="amount-currency">XAF</span>
                </div>
            </div>

            <!-- Footer avec QR -->
            <div class="receipt-footer">
                <div class="footer-details">
                    <h4>Informations d'Encaissement</h4>
                    <p><strong>Date d'émission:</strong> {{ now()->format('d/m/Y à H:i') }}</p>
                    @if($paiement->date_encaissement)
                    <p><strong>Date d'encaissement:</strong> {{ $paiement->date_encaissement->format('d/m/Y à H:i') }}</p>
                    @endif
                    @if($paiement->encaissePar)
                    <p><strong>Encaissé par:</strong> {{ $paiement->encaissePar->name }}</p>
                    @endif
                    @if($paiement->reference_paiement)
                    <p><strong>Référence:</strong> {{ $paiement->reference_paiement }}</p>
                    @endif
                </div>
                <div class="qr-section">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/paiements/' . $paiement->id)) }}" alt="QR Code">
                    <p>Scanner pour vérifier</p>
                </div>
            </div>

            <!-- Notes -->
            @if($paiement->notes)
            <div class="notes-section">
                <h5>📝 Notes</h5>
                <p>{{ $paiement->notes }}</p>
            </div>
            @endif

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <p>Signature du Caissier</p>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <p>Signature du Client</p>
                </div>
            </div>
        </div>

        <!-- Pied de page document -->
        <div class="document-footer">
            <p>Ce document est un reçu officiel de paiement • MedConnect © {{ date('Y') }} • Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
