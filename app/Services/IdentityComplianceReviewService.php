<?php

namespace App\Services;

use App\Ai\Agents\IdentityComplianceAgent;
use App\Models\DossierMedical;
use App\Models\DossierProfessionnel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Throwable;

class IdentityComplianceReviewService
{
    public function reviewMedicalSubmission(array $payload): array
    {
        $isDependant = (string) ($payload['declaration_mode'] ?? 'personnel') === 'dependant';
        $isAdult = $this->isAdult(Arr::get($payload, 'date_naissance'));
        $requiresIdentity = (! $isDependant) || $isAdult;
        $pieceNumber = trim((string) Arr::get($payload, 'numero_piece_identite', ''));

        $reasons = [];
        $score = 0;

        if ($requiresIdentity && trim((string) Arr::get($payload, 'type_piece_identite', '')) === '') {
            $score += 50;
            $reasons[] = 'Type de piece d identite manquant alors qu il est obligatoire.';
        }

        if ($requiresIdentity && $pieceNumber === '') {
            $score += 70;
            $reasons[] = 'Numero de piece d identite obligatoire et absent.';
        }

        if ($pieceNumber !== '' && DossierMedical::query()->where('numero_piece_identite', $pieceNumber)->exists()) {
            $score += 90;
            $reasons[] = 'Numero de piece deja utilise dans un autre dossier medical.';
        }

        if ($isDependant && ! $isAdult) {
            $reasons[] = 'Dossier mineur: piece d identite optionnelle jusqu a 18 ans.';
        }

        return $this->mergeWithAiReview('medical_submission', [
            'is_dependant' => $isDependant,
            'is_adult' => $isAdult,
            'requires_identity' => $requiresIdentity,
            'piece_type' => Arr::get($payload, 'type_piece_identite'),
            'piece_number' => $pieceNumber,
            'local_score' => $score,
            'local_reasons' => $reasons,
        ], $score, $reasons);
    }

    public function reviewMedicalDossier(DossierMedical $dossierMedical): array
    {
        $payload = [
            'declaration_mode' => $dossierMedical->est_personne_a_charge ? 'dependant' : 'personnel',
            'date_naissance' => optional($dossierMedical->date_naissance)?->toDateString(),
            'type_piece_identite' => $dossierMedical->type_piece_identite,
            'numero_piece_identite' => $dossierMedical->numero_piece_identite,
        ];

        $review = $this->reviewMedicalSubmission($payload);

        $duplicateCount = DossierMedical::query()
            ->where('numero_piece_identite', (string) $dossierMedical->numero_piece_identite)
            ->whereKeyNot($dossierMedical->id)
            ->count();

        if ($duplicateCount > 0) {
            $review['risk_level'] = 'high';
            $review['score'] = max((int) $review['score'], 90);
            $review['reasons'][] = 'Numero de piece partage avec '.$duplicateCount.' autre(s) dossier(s).';
        }

        $review['reasons'] = array_values(array_unique(array_filter($review['reasons'])));

        return $review;
    }

    public function reviewProfessionalSubmission(array $payload): array
    {
        $reasons = [];
        $score = 0;
        $niu = trim((string) Arr::get($payload, 'NIU', ''));

        if ($niu === '') {
            $score += 40;
            $reasons[] = 'NIU non renseigne pour le dossier professionnel.';
        }

        if ($niu !== '' && DossierProfessionnel::query()->where('NIU', $niu)->exists()) {
            $score += 90;
            $reasons[] = 'NIU deja utilise sur un autre dossier professionnel.';
        }

        if (trim((string) Arr::get($payload, 'specialite', '')) === '') {
            $score += 40;
            $reasons[] = 'Specialite manquante.';
        }

        if (! Arr::get($payload, 'has_identity_image')) {
            $score += 50;
            $reasons[] = 'Image d identite manquante.';
        }

        return $this->mergeWithAiReview('professional_submission', [
            'niu' => $niu,
            'specialite' => Arr::get($payload, 'specialite'),
            'type_structure' => Arr::get($payload, 'type_structure'),
            'has_identity_image' => (bool) Arr::get($payload, 'has_identity_image'),
            'local_score' => $score,
            'local_reasons' => $reasons,
        ], $score, $reasons);
    }

    public function reviewProfessionalDossier(DossierProfessionnel $dossierProfessionnel): array
    {
        $review = $this->reviewProfessionalSubmission([
            'NIU' => $dossierProfessionnel->NIU,
            'specialite' => $dossierProfessionnel->specialite,
            'type_structure' => $dossierProfessionnel->type_structure,
            'has_identity_image' => filled($dossierProfessionnel->image_identite_path),
        ]);

        $duplicateNiuCount = DossierProfessionnel::query()
            ->where('NIU', (string) $dossierProfessionnel->NIU)
            ->whereKeyNot($dossierProfessionnel->id)
            ->count();

        if ($duplicateNiuCount > 0) {
            $review['risk_level'] = 'high';
            $review['score'] = max((int) $review['score'], 90);
            $review['reasons'][] = 'NIU partage avec '.$duplicateNiuCount.' autre(s) dossier(s).';
        }

        $review['reasons'] = array_values(array_unique(array_filter($review['reasons'])));

        return $review;
    }

    private function mergeWithAiReview(string $scope, array $payload, int $localScore, array $localReasons): array
    {
        $riskLevel = $this->riskFromScore($localScore);
        $aiReview = $this->askAi($scope, $payload);

        if ($aiReview !== null) {
            $riskLevel = $this->maxRiskLevel($riskLevel, (string) ($aiReview['risk_level'] ?? 'low'));
            $localScore = max($localScore, (int) ($aiReview['score'] ?? 0));
            $localReasons = array_values(array_unique(array_merge(
                $localReasons,
                Arr::wrap($aiReview['reasons'] ?? [])
            )));
        }

        return [
            'risk_level' => $riskLevel,
            'score' => $localScore,
            'reasons' => array_values(array_filter($localReasons)),
            'source' => $aiReview ? 'local+ia' : 'local',
        ];
    }

    private function askAi(string $scope, array $payload): ?array
    {
        $provider = $this->resolveProvider();

        if ($provider === null) {
            return null;
        }

        $prompt = "Analyse ce dossier de conformite d identite (scope: {$scope}).\n"
            ."Contexte JSON:\n".json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n"
            .'Retourne uniquement un JSON valide avec la structure: '
            .'{"risk_level":"low|medium|high","score":0,"reasons":[""]}.';

        try {
            $response = IdentityComplianceAgent::make()->prompt($prompt, provider: $provider);
            $decoded = json_decode(trim((string) $response->text), true);

            if (! is_array($decoded)) {
                return null;
            }

            return [
                'risk_level' => (string) ($decoded['risk_level'] ?? 'low'),
                'score' => (int) ($decoded['score'] ?? 0),
                'reasons' => array_values(array_filter(Arr::wrap($decoded['reasons'] ?? []))),
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function resolveProvider(): ?string
    {
        $provider = (string) config('ai.default', 'gemini');
        $key = (string) data_get(config('ai.providers'), $provider.'.key', '');

        return $key !== '' ? $provider : null;
    }

    private function isAdult(?string $dateNaissance): bool
    {
        if (! $dateNaissance) {
            return true;
        }

        try {
            return Carbon::parse($dateNaissance)->age >= 18;
        } catch (Throwable) {
            return true;
        }
    }

    private function riskFromScore(int $score): string
    {
        if ($score >= 80) {
            return 'high';
        }

        if ($score >= 40) {
            return 'medium';
        }

        return 'low';
    }

    private function maxRiskLevel(string $left, string $right): string
    {
        $weights = ['low' => 1, 'medium' => 2, 'high' => 3];

        return ($weights[$left] ?? 1) >= ($weights[$right] ?? 1) ? $left : $right;
    }
}
