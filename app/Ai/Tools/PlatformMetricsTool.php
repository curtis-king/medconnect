<?php

namespace App\Ai\Tools;

use App\Models\FactureProfessionnelle;
use App\Models\RendezVousProfessionnel;
use App\Models\SoumissionMutuelle;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Carbon;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class PlatformMetricsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Analyze platform health metrics including invoices, mutuelle submission workflow, delayed backoffice processing, and appointments, then return a JSON summary.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $staleInvoiceDays = max(1, (int) $request->integer('stale_invoice_days', 7));
        $staleBackofficeDays = max(1, (int) $request->integer('stale_backoffice_days', 5));
        $upcomingWindowDays = max(1, (int) $request->integer('upcoming_window_days', 2));

        $staleInvoiceThreshold = now()->subDays($staleInvoiceDays);
        $staleBackofficeThreshold = now()->subDays($staleBackofficeDays);
        $upcomingWindowLimit = now()->copy()->addDays($upcomingWindowDays);

        $pendingInvoicesQuery = FactureProfessionnelle::query()
            ->where('statut_paiement_patient', 'en_attente');

        $stalePendingInvoicesQuery = (clone $pendingInvoicesQuery)
            ->where('created_at', '<=', $staleInvoiceThreshold);

        $delayedBackofficeSubmissionsQuery = SoumissionMutuelle::query()
            ->whereIn('statut', ['soumis', 'en_traitement'])
            ->where('date_soumission', '<=', $staleBackofficeThreshold);

        $pastDueAppointmentsQuery = RendezVousProfessionnel::query()
            ->whereIn('statut', ['en_attente', 'accepte'])
            ->where('date_proposee', '<', now());

        $upcomingAppointmentsQuery = RendezVousProfessionnel::query()
            ->whereIn('statut', ['en_attente', 'accepte'])
            ->whereBetween('date_proposee', [now(), $upcomingWindowLimit]);

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'thresholds' => [
                'stale_invoice_days' => $staleInvoiceDays,
                'stale_backoffice_days' => $staleBackofficeDays,
                'upcoming_window_days' => $upcomingWindowDays,
            ],
            'invoices' => [
                'pending_total_count' => (clone $pendingInvoicesQuery)->count(),
                'pending_total_amount_xaf' => (float) (clone $pendingInvoicesQuery)->sum('montant_total'),
                'stale_count' => (clone $stalePendingInvoicesQuery)->count(),
                'stale_amount_xaf' => (float) (clone $stalePendingInvoicesQuery)->sum('montant_total'),
                'oldest_pending_date' => $this->formatDate((clone $pendingInvoicesQuery)->oldest('created_at')->value('created_at')),
            ],
            'backoffice' => [
                'delayed_submission_count' => (clone $delayedBackofficeSubmissionsQuery)->count(),
                'delayed_submission_amount_xaf' => (float) (clone $delayedBackofficeSubmissionsQuery)->sum('montant_soumis'),
                'submitted_count' => SoumissionMutuelle::query()->where('statut', 'soumis')->count(),
                'processing_count' => SoumissionMutuelle::query()->where('statut', 'en_traitement')->count(),
                'approved_waiting_payment_count' => SoumissionMutuelle::query()
                    ->whereIn('statut', ['approuve', 'partiel'])
                    ->whereHas('factureProfessionnelle', function ($builder): void {
                        $builder->where('statut_backoffice', 'valide');
                    })
                    ->count(),
                'rejected_count' => SoumissionMutuelle::query()->where('statut', 'rejete')->count(),
                'paid_count' => SoumissionMutuelle::query()
                    ->whereHas('factureProfessionnelle', function ($builder): void {
                        $builder->where('statut_backoffice', 'paye');
                    })
                    ->count(),
            ],
            'appointments' => [
                'past_due_count' => (clone $pastDueAppointmentsQuery)->count(),
                'upcoming_count' => (clone $upcomingAppointmentsQuery)->count(),
            ],
        ];

        return json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'stale_invoice_days' => $schema->integer()->min(1)->description('Invoices older than this threshold are considered stale.'),
            'stale_backoffice_days' => $schema->integer()->min(1)->description('Backoffice submissions older than this threshold are considered delayed.'),
            'upcoming_window_days' => $schema->integer()->min(1)->description('Upcoming appointment analysis window in days.'),
        ];
    }

    private function formatDate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value)->toIso8601String();
    }
}
