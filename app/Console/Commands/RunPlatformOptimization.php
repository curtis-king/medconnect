<?php

namespace App\Console\Commands;

use App\Ai\Agents\PlatformOptimizationAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunPlatformOptimization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-platform-optimization
        {--provider=gemini : AI provider to use}
        {--stale-invoice-days=7 : Threshold in days for stale pending invoices}
        {--stale-backoffice-days=5 : Threshold in days for delayed backoffice submissions}
        {--upcoming-window-days=2 : Days window for upcoming appointments analysis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run autonomous platform optimization analysis with AI and log prioritized recommendations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $provider = (string) $this->option('provider');
        $staleInvoiceDays = max(1, (int) $this->option('stale-invoice-days'));
        $staleBackofficeDays = max(1, (int) $this->option('stale-backoffice-days'));
        $upcomingWindowDays = max(1, (int) $this->option('upcoming-window-days'));

        if ($provider === 'gemini' && blank((string) config('ai.providers.gemini.key'))) {
            $this->error('GEMINI_API_KEY est vide. Ajoutez votre cle dans le fichier .env puis lancez php artisan config:clear.');

            return self::FAILURE;
        }

        $prompt = <<<PROMPT
Analyse l'etat operationnel de la plateforme et retourne un plan d'optimisation priorise.

Contraintes:
- Commence par appeler l'outil PlatformMetricsTool.
- Utilise ces parametres d'analyse:
  - stale_invoice_days: {$staleInvoiceDays}
  - stale_backoffice_days: {$staleBackofficeDays}
  - upcoming_window_days: {$upcomingWindowDays}
- Propose des actions realistes executables par l'equipe produit/backoffice.
PROMPT;

        try {
            $response = PlatformOptimizationAgent::make()->prompt($prompt, provider: $provider);

            $this->info('=== Rapport IA d\'optimisation ===');
            $this->line($response->text);

            Log::info('Platform optimization report generated', [
                'provider' => $provider,
                'stale_invoice_days' => $staleInvoiceDays,
                'stale_backoffice_days' => $staleBackofficeDays,
                'upcoming_window_days' => $upcomingWindowDays,
                'report' => $response->text,
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Echec de l\'analyse IA: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
