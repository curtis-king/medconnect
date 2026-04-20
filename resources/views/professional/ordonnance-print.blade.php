<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance #{{ $ordonnance->id ?? $consultation->id }}</title>
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #ccfbf1;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #dbe4e6;
            --card: #ffffff;
            --paper: #f4f8f8;
            --danger-soft: #fee2e2;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg, #eef6f6 0%, #f9fcfc 100%);
            color: var(--ink);
        }

        .print-actions {
            max-width: 920px;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
        }

        .btn-muted {
            border: 1px solid var(--line);
            background: #fff;
            color: #1f2937;
        }

        .sheet {
            max-width: 920px;
            margin: 0 auto;
            background: var(--paper);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 22px 54px rgba(15, 118, 110, 0.12);
        }

        .header {
            background: linear-gradient(130deg, #0f766e 0%, #14b8a6 55%, #22d3ee 100%);
            color: #fff;
            padding: 26px 28px 22px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
        }

        .clinic {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-mark {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.15);
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
        }

        .clinic h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.02em;
        }

        .clinic p {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.92;
        }

        .meta {
            text-align: right;
            align-self: start;
        }

        .meta .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.18);
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .meta p {
            margin: 8px 0 0;
            font-size: 12px;
            opacity: 0.95;
        }

        .body {
            padding: 22px;
            display: grid;
            gap: 16px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 13px;
        }

        .label {
            margin: 0 0 4px;
            color: var(--muted);
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .value {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.45;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 15px;
        }

        .panel-title {
            margin: 0 0 10px;
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #0f766e;
            font-weight: 800;
        }

        .rx-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .rx-item {
            padding: 10px 11px;
            border: 1px solid #d6ece8;
            border-left: 4px solid #0f766e;
            border-radius: 9px;
            background: #f8fffd;
            font-size: 14px;
            line-height: 1.45;
        }

        .text-block {
            margin: 0;
            font-size: 14px;
            line-height: 1.55;
            white-space: pre-wrap;
        }

        .signature-wrap {
            margin-top: 4px;
            display: grid;
            grid-template-columns: 1fr 270px;
            gap: 12px;
            align-items: stretch;
        }

        .legal {
            padding: 12px 13px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            line-height: 1.5;
            color: #64748b;
        }

        .signature {
            border: 1px dashed #94a3b8;
            border-radius: 12px;
            background: repeating-linear-gradient(
                -45deg,
                #f8fafc,
                #f8fafc 10px,
                #f1f5f9 10px,
                #f1f5f9 20px
            );
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 118px;
        }

        .signature p {
            margin: 0;
            font-size: 12px;
            color: #334155;
            text-align: center;
        }

        .line {
            margin-top: 36px;
            border-top: 1px solid #64748b;
            height: 1px;
        }

        .footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 12px 22px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 11px;
            color: #64748b;
        }

        .empty {
            padding: 10px;
            border-radius: 9px;
            background: var(--danger-soft);
            color: #991b1b;
            font-size: 13px;
        }

        @media (max-width: 760px) {
            body {
                padding: 10px;
            }

            .header {
                grid-template-columns: 1fr;
            }

            .meta {
                text-align: left;
            }

            .cards,
            .signature-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            @page {
                margin: 11mm;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .sheet {
                border: none;
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
            }

            .panel,
            .card,
            .signature,
            .legal {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
        <button onclick="window.close()" class="btn btn-muted">Fermer</button>
    </div>

    <div class="sheet">
        <header class="header">
            <div class="clinic">
                <div class="logo-mark">Rx</div>
                <div>
                    <h1>Ordonnance Medicale</h1>
                    <p>{{ $dossierProfessionnel->raison_sociale ?? $dossierProfessionnel->user?->name ?? config('app.name') }}</p>
                </div>
            </div>
            <div class="meta">
                <span class="badge">Prescription</span>
                <p>Ref: ORD-{{ $ordonnance->id ?? $consultation->id }}</p>
                <p>Date: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </header>

        <main class="body">
            <section class="cards">
                <article class="card">
                    <p class="label">Patient</p>
                    <p class="value">{{ $consultation->patient?->name ?? '—' }}</p>
                </article>
                <article class="card">
                    <p class="label">Dossier medical</p>
                    <p class="value">{{ $consultation->dossierMedical?->numero_unique ?? '—' }}</p>
                </article>
                <article class="card">
                    <p class="label">Professionnel</p>
                    <p class="value">{{ $dossierProfessionnel->user?->name ?? '—' }}</p>
                </article>
                <article class="card">
                    <p class="label">Consultation</p>
                    <p class="value">CONS-{{ $consultation->id }}</p>
                </article>
            </section>

            <section class="panel">
                <h2 class="panel-title">Medicaments prescrits</h2>
                @if(!empty($ordonnance->produits))
                    <ul class="rx-list">
                        @foreach($ordonnance->produits as $produit)
                            <li class="rx-item">{{ $produit }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="empty">Aucun medicament renseigne sur cette ordonnance.</p>
                @endif
            </section>

            <section class="panel">
                <h2 class="panel-title">Prescription</h2>
                <p class="text-block">{{ $ordonnance->prescription ?? '—' }}</p>
            </section>

            <section class="panel">
                <h2 class="panel-title">Recommandations</h2>
                <p class="text-block">{{ $ordonnance->recommandations ?? '—' }}</p>
            </section>

            <section class="panel">
                <h2 class="panel-title">Instructions complementaires</h2>
                <p class="text-block">{{ $ordonnance->instructions_complementaires ?? '—' }}</p>
            </section>

            <section class="signature-wrap">
                <div class="legal">
                    Document medical confidentiel. Cette ordonnance est emise dans le cadre de la consultation CONS-{{ $consultation->id }}.
                    Respecter strictement la posologie et consulter le professionnel en cas de reaction inhabituelle.
                </div>
                <div class="signature">
                    <div class="line"></div>
                    <p>Signature et cachet du professionnel</p>
                </div>
            </section>
        </main>

        <footer class="footer">
            <span>{{ config('app.name') }}</span>
            <span>Imprime le {{ now()->format('d/m/Y a H:i') }}</span>
        </footer>
    </div>
</body>
</html>
