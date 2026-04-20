<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synthèse complète #{{ $consultation->id }}</title>
    <style>
        :root {
            --brand: #0f766e;
            --soft: #ecfeff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #d8e6e7;
            --card: #fff;
            --paper: #f7fcfb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #edf8f7 0%, #f8fcfc 100%);
        }
        .actions {
            max-width: 1040px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn {
            border: 0;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-main { background: var(--brand); color: #fff; }
        .btn-alt { border: 1px solid var(--line); background: #fff; color: #1f2937; }
        .sheet {
            max-width: 1040px;
            margin: 0 auto;
            border: 1px solid #d2eeeb;
            background: var(--paper);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 118, 110, 0.12);
        }
        .head {
            background: linear-gradient(140deg, #0f766e 0%, #14b8a6 50%, #2dd4bf 100%);
            color: #fff;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .head h1 { margin: 0; font-size: 25px; }
        .head p { margin: 6px 0 0; font-size: 13px; opacity: 0.92; }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.4);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .body { padding: 20px; display: grid; gap: 14px; }
        .section {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            padding: 14px;
        }
        .section h2 {
            margin: 0 0 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .item {
            border: 1px solid #e1eeee;
            border-radius: 11px;
            background: #fcfefe;
            padding: 10px;
        }
        .label {
            margin: 0 0 4px;
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
        }
        .value {
            margin: 0;
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 600;
        }
        ul.clean {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
        }
        .footer {
            border-top: 1px solid #d2eeeb;
            background: #fff;
            color: #475569;
            font-size: 11px;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .sheet { border: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn btn-alt" onclick="window.close()">Fermer</button>
        <button class="btn btn-main" onclick="window.print()">Imprimer</button>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <h1>Synthèse médicale complète</h1>
                <p>{{ config('app.name') }} · {{ $dossierProfessionnel->user?->name ?? 'Professionnel' }}</p>
            </div>
            <div style="text-align:right;">
                <span class="badge">{{ $consultation->statut }}</span>
                <p style="margin-top:10px;">Réf #{{ $consultation->numero_dossier_reference ?? $consultation->id }}</p>
            </div>
        </div>

        <div class="body">
            <div class="section">
                <h2>Identité consultation</h2>
                <div class="grid">
                    <div class="item"><p class="label">Patient</p><p class="value">{{ $consultation->patient?->name ?? 'N/A' }}</p></div>
                    <div class="item"><p class="label">Date RDV</p><p class="value">{{ optional($consultation->rendezVous?->date_proposee)->format('d/m/Y H:i') ?? 'N/A' }}</p></div>
                    <div class="item"><p class="label">Mode</p><p class="value">{{ $consultation->type_consultation ?? 'N/A' }}</p></div>
                    <div class="item"><p class="label">Service</p><p class="value">{{ $consultation->rendezVous?->serviceProfessionnel?->nom ?? $consultation->type_service ?? 'N/A' }}</p></div>
                </div>
            </div>

            <div class="section">
                <h2>Consultation</h2>
                <div class="grid">
                    <div class="item"><p class="label">Constantes</p><p class="value">Temp: {{ $consultation->temperature ?? 'N/A' }} °C
Tension: {{ $consultation->tension_arterielle ?? 'N/A' }}
Glycémie: {{ $consultation->taux_glycemie ?? 'N/A' }} g/L
Poids: {{ $consultation->poids ?? 'N/A' }} kg</p></div>
                    <div class="item"><p class="label">Diagnostic médecin</p><p class="value">{{ $consultation->diagnostic_medecin ?: 'Non renseigné' }}</p></div>
                    <div class="item"><p class="label">Diagnostic complémentaire</p><p class="value">{{ $consultation->diagnostic ?: 'Non renseigné' }}</p></div>
                    <div class="item"><p class="label">Conclusion / recommandations</p><p class="value">{{ $consultation->conclusion ?: 'Non renseigné' }}

{{ $consultation->recommandations ?: '' }}</p></div>
                </div>
            </div>

            <div class="section">
                <h2>Ordonnance / prescription</h2>
                @if($ordonnance)
                    <div class="grid">
                        <div class="item">
                            <p class="label">Produits prescrits</p>
                            @if(collect($ordonnance->produits ?? [])->isNotEmpty())
                                <ul class="clean">
                                    @foreach(collect($ordonnance->produits ?? []) as $produit)
                                        <li>{{ $produit }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="value">Aucun produit listé.</p>
                            @endif
                        </div>
                        <div class="item"><p class="label">Prescription détaillée</p><p class="value">{{ $ordonnance->prescription ?: 'Non renseigné' }}</p></div>
                        <div class="item"><p class="label">Recommandations</p><p class="value">{{ $ordonnance->recommandations ?: 'Non renseigné' }}</p></div>
                        <div class="item"><p class="label">Instructions complémentaires</p><p class="value">{{ $ordonnance->instructions_complementaires ?: 'Non renseigné' }}</p></div>
                    </div>
                @else
                    <p class="value">Aucune ordonnance disponible.</p>
                @endif
            </div>

            <div class="section">
                <h2>Examens & Documents</h2>
                <div class="grid">
                    <div class="item">
                        <p class="label">Examens prescrits ({{ $consultation->examens->count() }})</p>
                        @if($consultation->examens->isNotEmpty())
                            <ul class="clean">
                                @foreach($consultation->examens as $examen)
                                    <li>{{ $examen->libelle }} · {{ $examen->statut }} · {{ $examen->dossierProfessionnel?->user?->name ?? 'N/A' }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="value">Aucun examen.</p>
                        @endif
                    </div>
                    <div class="item">
                        <p class="label">Documents ({{ $consultation->documents->count() }})</p>
                        @if($consultation->documents->isNotEmpty())
                            <ul class="clean">
                                @foreach($consultation->documents as $document)
                                    <li>{{ $document->nom_fichier }} · {{ $document->source }} · {{ optional($document->created_at)->format('d/m/Y H:i') }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="value">Aucun document.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <span>Document généré le {{ now()->format('d/m/Y H:i') }}</span>
            <span>Confidentiel - synthèse professionnelle</span>
        </div>
    </div>
</body>
</html>
