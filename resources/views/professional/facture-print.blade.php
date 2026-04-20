<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .muted { color: #6b7280; }
        .card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .amount { font-size: 24px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()" style="padding: 8px 14px; border: none; border-radius: 8px; background: #0891b2; color: #fff; cursor: pointer;">Imprimer</button>
    </div>

    <div class="header">
        <div>
            <h1 style="margin: 0;">Facture physique</h1>
            <p class="muted" style="margin: 4px 0 0 0;">Reference: {{ $facture->reference }}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-weight: 600;">{{ $dossierProfessionnel->raison_sociale ?? 'Professionnel de sante' }}</p>
            <p class="muted" style="margin: 4px 0 0 0;">Specialite: {{ $dossierProfessionnel->specialite ?? '—' }}</p>
        </div>
    </div>

    <div class="card grid">
        <div>
            <p class="muted" style="margin: 0;">Patient</p>
            <p style="margin: 4px 0 0 0; font-weight: 600;">{{ $facture->patient?->name ?? (($facture->dossierMedical?->prenom ?? '') . ' ' . ($facture->dossierMedical?->nom ?? '')) }}</p>
            <p class="muted" style="margin: 4px 0 0 0;">Dossier: {{ $facture->numero_dossier_reference ?? $facture->dossierMedical?->numero_unique ?? '—' }}</p>
        </div>
        <div>
            <p class="muted" style="margin: 0;">Date emission</p>
            <p style="margin: 4px 0 0 0; font-weight: 600;">{{ optional($facture->emise_le ?? $facture->created_at)->format('d/m/Y H:i') }}</p>
            <p class="muted" style="margin: 4px 0 0 0;">Type: {{ ucfirst((string) $facture->type_facture) }}</p>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Designation</th>
                    <th>Montant</th>
                    <th>Statut patient</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $facture->serviceProfessionnel?->nom ?? ucfirst((string) $facture->type_service) }}</td>
                    <td>{{ number_format((float) $facture->montant_total, 0, ',', ' ') }} XAF</td>
                    <td>{{ ucfirst((string) $facture->statut_paiement_patient) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <p class="muted" style="margin: 0;">Total a payer</p>
        <p class="amount" style="margin: 6px 0 0 0;">{{ number_format((float) $facture->montant_a_charge_patient, 0, ',', ' ') }} XAF</p>
        <p class="muted" style="margin: 8px 0 0 0;">Mode paiement: {{ $facture->mode_paiement ? ucfirst((string) $facture->mode_paiement) : 'Non precise' }}</p>
    </div>
</body>
</html>
