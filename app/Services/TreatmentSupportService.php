<?php

namespace App\Services;

use App\Ai\Agents\TreatmentSupportAgent;
use App\Models\ConsultationProfessionnelle;
use App\Models\OrdonnanceProfessionnelle;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class TreatmentSupportService
{
    public function analyzeForPatient(OrdonnanceProfessionnelle $ordonnance): array
    {
        $fallback = $this->buildPatientFallback($ordonnance);
        $provider = $this->resolveProvider();

        if ($provider === null) {
            return $fallback;
        }

        $payload = [
            'produits' => array_values(array_filter(array_map('trim', $ordonnance->produits ?? []))),
            'prescription' => $ordonnance->prescription,
            'recommandations' => $ordonnance->recommandations,
            'instructions' => $ordonnance->instructions_complementaires,
        ];

        $prompt = <<<PROMPT
Analyse cette ordonnance pour aider un patient a suivre son traitement.

Contexte JSON:
{$this->toJson($payload)}

Retourne uniquement un JSON valide avec cette structure:
{
  "resume": "texte court",
  "duree_estimee_jours": 0,
  "periode_resume": "texte court ou null",
  "prises": [{"medicament": "", "consigne": "", "duree_jours": 0, "duree_texte": ""}],
  "conseils": [""],
  "points_attention": [""]
}

Contraintes:
- langage simple pour patient.
- si la duree est inconnue, utilise null.
- ne pas inventer de molecule absente.
- si une information manque, le signaler simplement.
PROMPT;

        try {
            $response = TreatmentSupportAgent::make()->prompt($prompt, provider: $provider);
            $decoded = $this->decodeJson((string) $response->text);

            if (! is_array($decoded)) {
                return $fallback;
            }

            return [
                'source' => 'assistant_ia',
                'resume' => (string) ($decoded['resume'] ?? $fallback['resume']),
                'duree_estimee_jours' => $this->normalizeNullableInt($decoded['duree_estimee_jours'] ?? $fallback['duree_estimee_jours']),
                'periode_resume' => $this->normalizeNullableString($decoded['periode_resume'] ?? $fallback['periode_resume']),
                'prises' => $this->normalizePrises($decoded['prises'] ?? $fallback['prises']),
                'conseils' => $this->normalizeStringList($decoded['conseils'] ?? $fallback['conseils']),
                'points_attention' => $this->normalizeStringList($decoded['points_attention'] ?? $fallback['points_attention']),
            ];
        } catch (Throwable) {
            return $fallback;
        }
    }

    public function suggestForProfessional(ConsultationProfessionnelle $consultation, array $input): array
    {
        $fallback = $this->buildProfessionalFallback($consultation, $input);
        $provider = $this->resolveProvider();

        if ($provider === null) {
            return $fallback;
        }

        $payload = [
            'symptomes' => $input['symptomes'] ?? $consultation->symptomes,
            'diagnostic_medecin' => $input['diagnostic_medecin'] ?? $consultation->diagnostic_medecin,
            'diagnostic' => $input['diagnostic'] ?? $consultation->diagnostic,
            'conclusion' => $input['conclusion'] ?? $consultation->conclusion,
            'ordonnance_produits' => $this->normalizeTextareaLines($input['ordonnance_produits'] ?? null),
            'ordonnance_prescription' => $input['ordonnance_prescription'] ?? null,
            'ordonnance_recommandations' => $input['ordonnance_recommandations'] ?? null,
            'ordonnance_instructions' => $input['ordonnance_instructions'] ?? null,
        ];

        $prompt = <<<PROMPT
Tu aides un professionnel de sante a preparer une proposition de traitement a la demande.

Contexte JSON:
{$this->toJson($payload)}

Retourne uniquement un JSON valide avec cette structure:
{
  "resume_clinique": "texte court",
  "ordonnance_proposee": [""],
  "prescription_proposee": "",
  "recommandations_proposees": "",
  "suivi_propose": "",
  "points_attention": [""]
}

Contraintes:
- suggestions uniquement, jamais une certitude.
- pas de diagnostic final affirme.
- rester concis, exploitable, et en francais.
- si des donnees manquent, proposer ce qu'il faut verifier avant validation.
PROMPT;

        try {
            $response = TreatmentSupportAgent::make()->prompt($prompt, provider: $provider);
            $decoded = $this->decodeJson((string) $response->text);

            if (! is_array($decoded)) {
                return $fallback;
            }

            return [
                'source' => 'assistant_ia',
                'resume_clinique' => (string) ($decoded['resume_clinique'] ?? $fallback['resume_clinique']),
                'ordonnance_proposee' => $this->normalizeStringList($decoded['ordonnance_proposee'] ?? $fallback['ordonnance_proposee']),
                'prescription_proposee' => (string) ($decoded['prescription_proposee'] ?? $fallback['prescription_proposee']),
                'recommandations_proposees' => (string) ($decoded['recommandations_proposees'] ?? $fallback['recommandations_proposees']),
                'suivi_propose' => (string) ($decoded['suivi_propose'] ?? $fallback['suivi_propose']),
                'points_attention' => $this->normalizeStringList($decoded['points_attention'] ?? $fallback['points_attention']),
            ];
        } catch (Throwable) {
            return $fallback;
        }
    }

    private function buildPatientFallback(OrdonnanceProfessionnelle $ordonnance): array
    {
        $lines = collect($ordonnance->produits ?? [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values();

        $prises = $lines
            ->map(function (string $line): array {
                [$durationDays, $durationText] = $this->extractDuration($line);

                return [
                    'medicament' => Str::before($line, ' - ') !== $line ? trim(Str::before($line, ' - ')) : $line,
                    'consigne' => $line,
                    'duree_jours' => $durationDays,
                    'duree_texte' => $durationText,
                ];
            })
            ->all();

        $knownDurations = collect($prises)
            ->pluck('duree_jours')
            ->filter(fn ($value) => is_int($value) && $value > 0);

        $durationDays = $knownDurations->isNotEmpty() ? (int) $knownDurations->max() : null;
        $periodSummary = $durationDays ? 'Suivi estime sur '.$durationDays.' jour(s).' : 'Duree precise non retrouvee dans l ordonnance.';
        $resume = $lines->isNotEmpty()
            ? 'L ordonnance mentionne '.count($prises).' traitement(s). '.$periodSummary
            : 'Aucun medicament structure n a ete retrouve. Verifiez la prescription detaillee.';

        $conseils = [
            'Respectez les horaires et la duree indiquee par le professionnel de sante.',
            'Ne stoppez pas le traitement en avance sans avis medical.',
        ];

        if (filled($ordonnance->instructions_complementaires)) {
            $conseils[] = 'Consignes complementaires: '.trim((string) $ordonnance->instructions_complementaires);
        }

        $pointsAttention = [];

        if (blank($ordonnance->prescription) && $lines->isEmpty()) {
            $pointsAttention[] = 'Prescription insuffisamment detaillee pour calculer une duree fiable.';
        }

        if ($durationDays === null) {
            $pointsAttention[] = 'La duree de traitement n a pas ete detectee automatiquement. Confirmez-la avec votre professionnel si besoin.';
        }

        return [
            'source' => 'analyse_locale',
            'resume' => $resume,
            'duree_estimee_jours' => $durationDays,
            'periode_resume' => $periodSummary,
            'prises' => $prises,
            'conseils' => $conseils,
            'points_attention' => $pointsAttention,
        ];
    }

    private function buildProfessionalFallback(ConsultationProfessionnelle $consultation, array $input): array
    {
        $symptoms = trim((string) ($input['symptomes'] ?? $consultation->symptomes ?? ''));
        $doctorAssessment = trim((string) ($input['diagnostic_medecin'] ?? $consultation->diagnostic_medecin ?? ''));
        $supportingDiagnosis = trim((string) ($input['diagnostic'] ?? $consultation->diagnostic ?? ''));
        $ordonnanceLines = $this->normalizeTextareaLines($input['ordonnance_produits'] ?? '');

        $resume = collect([
            $symptoms !== '' ? 'Symptomes: '.$symptoms : null,
            $doctorAssessment !== '' ? 'Diagnostic medecin: '.$doctorAssessment : null,
            $supportingDiagnosis !== '' ? 'Diagnostic complementaire: '.$supportingDiagnosis : null,
        ])->filter()->implode(' | ');

        if ($resume === '') {
            $resume = 'Donnees cliniques insuffisantes pour proposer une suggestion riche. Completer symptomes et hypothese clinique.';
        }

        $pointsAttention = [];

        if ($symptoms === '') {
            $pointsAttention[] = 'Renseigner les symptomes pour fiabiliser la suggestion.';
        }

        if ($doctorAssessment === '' && $supportingDiagnosis === '') {
            $pointsAttention[] = 'Ajouter une hypothese clinique avant validation finale.';
        }

        if ($ordonnanceLines === []) {
            $pointsAttention[] = 'Aucune ligne de traitement n est encore renseignee.';
        }

        return [
            'source' => 'analyse_locale',
            'resume_clinique' => $resume,
            'ordonnance_proposee' => $ordonnanceLines,
            'prescription_proposee' => (string) ($input['ordonnance_prescription'] ?? ''),
            'recommandations_proposees' => (string) ($input['ordonnance_recommandations'] ?? $input['recommandations'] ?? ''),
            'suivi_propose' => (string) ($input['ordonnance_instructions'] ?? 'Verifier adherence, tolérance et evolution clinique selon la pathologie suspectee.'),
            'points_attention' => $pointsAttention,
        ];
    }

    private function resolveProvider(): ?string
    {
        $provider = (string) config('ai.default', 'gemini');
        $key = (string) data_get(config('ai.providers'), $provider.'.key', '');

        return $key !== '' ? $provider : null;
    }

    private function decodeJson(string $content): ?array
    {
        $trimmed = trim($content);
        $trimmed = preg_replace('/^```json\s*|^```\s*|\s*```$/m', '', $trimmed) ?? $trimmed;
        $decoded = json_decode($trimmed, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function toJson(array $payload): string
    {
        return (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function extractDuration(string $text): array
    {
        $normalized = Str::of($text)->lower()->ascii()->value();

        if (! preg_match('/(\d{1,3})\s*(jours|jour|semaines|semaine|mois|j)/', $normalized, $matches)) {
            return [null, null];
        }

        $value = (int) $matches[1];
        $unit = $matches[2];
        $days = match ($unit) {
            'jour', 'jours', 'j' => $value,
            'semaine', 'semaines' => $value * 7,
            'mois' => $value * 30,
            default => null,
        };

        return [$days, $matches[0]];
    }

    private function normalizeNullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    private function normalizeStringList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return collect(Arr::wrap($value))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizePrises(mixed $value): array
    {
        return collect(Arr::wrap($value))
            ->map(function ($item): array {
                if (! is_array($item)) {
                    $item = ['consigne' => (string) $item];
                }

                return [
                    'medicament' => trim((string) ($item['medicament'] ?? 'Traitement')),
                    'consigne' => trim((string) ($item['consigne'] ?? '')),
                    'duree_jours' => $this->normalizeNullableInt($item['duree_jours'] ?? null),
                    'duree_texte' => $this->normalizeNullableString($item['duree_texte'] ?? null),
                ];
            })
            ->filter(fn (array $item) => $item['medicament'] !== '' || $item['consigne'] !== '')
            ->values()
            ->all();
    }

    private function normalizeTextareaLines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }
}
