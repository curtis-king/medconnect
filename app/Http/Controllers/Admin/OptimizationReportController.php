<?php

namespace App\Http\Controllers\Admin;

use App\Ai\Agents\PlatformOptimizationAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOptimizationReportActionRequest;
use App\Models\OptimizationReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class OptimizationReportController extends Controller
{
    public function index(): View
    {
        $reports = OptimizationReport::latest('generated_at')->paginate(15);

        return view('admin.optimization-reports.index', [
            'reports' => $reports,
        ]);
    }

    public function show(OptimizationReport $report): View
    {
        return view('admin.optimization-reports.show', [
            'report' => $report,
        ]);
    }

    public function create(): View
    {
        $recentReports = OptimizationReport::query()
            ->whereNotNull('metrics')
            ->latest('generated_at')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        $chartPreview = [
            'labels' => $recentReports->map(fn (OptimizationReport $report) => $report->generated_at?->format('d/m H:i') ?? 'N/A')->values(),
            'pendingInvoices' => $recentReports->map(fn (OptimizationReport $report) => (int) ($report->metrics['invoices']['pending_total_count'] ?? 0))->values(),
            'pastDueAppointments' => $recentReports->map(fn (OptimizationReport $report) => (int) ($report->metrics['appointments']['past_due_count'] ?? 0))->values(),
            'delayedSubmissions' => $recentReports->map(fn (OptimizationReport $report) => (int) ($report->metrics['backoffice']['delayed_submission_count'] ?? 0))->values(),
        ];

        return view('admin.optimization-reports.create', [
            'defaults' => [
                'stale_invoice_days' => 7,
                'stale_backoffice_days' => 5,
                'upcoming_window_days' => 2,
                'provider' => 'gemini',
            ],
            'chartPreview' => $chartPreview,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stale_invoice_days' => 'required|integer|min:1|max:90',
            'stale_backoffice_days' => 'required|integer|min:1|max:90',
            'upcoming_window_days' => 'required|integer|min:1|max:30',
            'provider' => 'required|string|in:gemini,openai,anthropic',
            'generate_visual_stats' => 'nullable|boolean',
        ]);

        if (blank((string) config('ai.providers.'.$validated['provider'].'.key'))) {
            return back()->with('error', 'Clé API '.strtoupper($validated['provider']).' non configurée.');
        }

        try {
            $prompt = <<<PROMPT
Analyse l'etat operationnel de la plateforme et retourne un plan d'optimisation priorise.

Contraintes:
- Commence par appeler l'outil PlatformMetricsTool.
- Utilise ces parametres d'analyse:
  - stale_invoice_days: {$validated['stale_invoice_days']}
  - stale_backoffice_days: {$validated['stale_backoffice_days']}
  - upcoming_window_days: {$validated['upcoming_window_days']}
- Propose des actions realistes executables par l'equipe produit/backoffice.
PROMPT;

            $response = PlatformOptimizationAgent::make()->prompt($prompt, provider: $validated['provider']);

            $report = OptimizationReport::create([
                'stale_invoice_days' => $validated['stale_invoice_days'],
                'stale_backoffice_days' => $validated['stale_backoffice_days'],
                'upcoming_window_days' => $validated['upcoming_window_days'],
                'provider' => $validated['provider'],
                'content' => $response->text,
                'metrics' => $response->toolResults->map(fn ($result) => json_decode($result->content, true))->first(),
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            $withCharts = (bool) ($validated['generate_visual_stats'] ?? false);

            return redirect()->route('admin.optimization-reports.show', [
                'optimizationReport' => $report,
                'charts' => $withCharts ? 1 : 0,
            ])
                ->with('success', 'Rapport généré avec succès.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Erreur lors de la génération : '.$exception->getMessage());
        }
    }

    public function destroy(OptimizationReport $report): RedirectResponse
    {
        $report->delete();

        return redirect()->route('admin.optimization-reports.index')
            ->with('success', 'Rapport supprimé.');
    }

    public function updateAction(UpdateOptimizationReportActionRequest $request, OptimizationReport $report): RedirectResponse
    {
        $payload = $request->validated();

        $report->update([
            'admin_response' => $payload['admin_response'] ?? null,
            'action_plan' => $payload['action_plan'] ?? null,
            'action_status' => $payload['action_status'],
            'action_due_date' => $payload['action_due_date'] ?? null,
            'action_completed_at' => $payload['action_status'] === 'done' ? now() : null,
        ]);

        return back()->with('success', 'Retour et action mis à jour avec succès.');
    }
}
