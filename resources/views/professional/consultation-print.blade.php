<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation #{{ $consultation->id }}</title>
    <style>
        :root {
            --brand: #0b5ed7;
            --brand-soft: #e7f0ff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #d9e2ec;
            --card: #ffffff;
            --paper: #f8fbff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #eef4ff 0%, #f8fbff 100%);
        }
        .actions {
            max-width: 980px;
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
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-soft { background: #fff; border: 1px solid var(--line); color: #1f2937; }
        .sheet {
            max-width: 980px;
            margin: 0 auto;
            border: 1px solid #dbeafe;
            background: var(--paper);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(11, 94, 215, 0.12);
        }
        .head {
            background: linear-gradient(140deg, #0b5ed7 0%, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }
        .head h1 { margin: 0; font-size: 25px; }
        .head p { margin: 6px 0 0; font-size: 13px; opacity: 0.92; }
        .chip {
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.18);
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 800;
        }
        .body { padding: 22px; display: grid; gap: 14px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .card {
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 14px;
            padding: 12px;
        }
        .label {
            margin: 0 0 4px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 11px;
            font-weight: 700;
        }
        .value {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        .panel {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 14px;
            padding: 14px;
        }
        .title {
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 12px;
            color: var(--brand);
            font-weight: 800;
        }
        .foot {
            border-top: 1px solid #dbeafe;
            background: #fff;
            color: #475569;
            font-size: 11px;
            padding: 10px 22px;
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
        <button class="btn btn-soft" onclick="window.close()">Fermer</button>
        <button class="btn btn-primary" onclick="window.print()">Imprimer</button>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                <h1>Fiche de consultation</h1>
                <p>{{ config('app.name') }} · {{ $dossierProfessionnel->user?->name ?? 'Professionnel' }}</p>
            </div>
            <div>
                <span class="chip">{{ $consultation->statut }}</span>
                <p style="margin-top:10px; text-align:right;">Référence #{{ $consultation->numero_dossier_reference ?? $consultation->id }}</p>
            </div>
        </div>

        <div class="body">
            <div class="grid">
                <div class="card">
                    <p class="label">Patient</p>
                    <p class="value">{{ $consultation->patient?->name ?? 'N/A' }}</p>
                </div>
                <div class="card">
                    <p class="label">Date du rendez-vous</p>
                    <p class="value">{{ optional($consultation->rendezVous?->date_proposee)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="panel">
                <p class="title">Constantes</p>
                <div class="grid">
                    <div class="card"><p class="label">Température</p><p class="value">{{ $consultation->temperature ?? 'N/A' }} °C</p></div>
                    <div class="card"><p class="label">Tension</p><p class="value">{{ $consultation->tension_arterielle ?? 'N/A' }}</p></div>
                    <div class="card"><p class="label">Glycémie</p><p class="value">{{ $consultation->taux_glycemie ?? 'N/A' }} g/L</p></div>
                    <div class="card"><p class="label">Poids</p><p class="value">{{ $consultation->poids ?? 'N/A' }} kg</p></div>
                </div>
            </div>

            <div class="panel">
                <p class="title">Évaluation clinique</p>
                <div class="grid">
                    <div class="card"><p class="label">Symptômes</p><p class="value">{{ $consultation->symptomes ?: 'Non renseigné' }}</p></div>
                    <div class="card"><p class="label">Diagnostic médecin</p><p class="value">{{ $consultation->diagnostic_medecin ?: 'Non renseigné' }}</p></div>
                    <div class="card"><p class="label">Diagnostic complémentaire</p><p class="value">{{ $consultation->diagnostic ?: 'Non renseigné' }}</p></div>
                    <div class="card"><p class="label">Conclusion</p><p class="value">{{ $consultation->conclusion ?: 'Non renseigné' }}</p></div>
                </div>
            </div>

            <div class="panel">
                <p class="title">Résultats & Suivi</p>
                <div class="grid">
                    <div class="card"><p class="label">Note résultat</p><p class="value">{{ $consultation->note_resultat ?: 'Non renseigné' }}</p></div>
                    <div class="card"><p class="label">Fichier résultat</p><p class="value">{{ $consultation->fichier_resultat_path ?: 'Aucun fichier' }}</p></div>
                    <div class="card"><p class="label">Observations</p><p class="value">{{ $consultation->observations ?: 'Non renseigné' }}</p></div>
                    <div class="card"><p class="label">Recommandations</p><p class="value">{{ $consultation->recommandations ?: 'Non renseigné' }}</p></div>
                </div>
            </div>
        </div>

        <div class="foot">
            <span>Document généré le {{ now()->format('d/m/Y H:i') }}</span>
            <span>Confidentiel - usage médical</span>
        </div>
    </div>
</body>
</html>
