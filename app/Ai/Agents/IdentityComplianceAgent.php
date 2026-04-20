<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class IdentityComplianceAgent implements Agent, Conversational
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are an identity and compliance screening copilot for a healthcare platform.

Rules:
- Write in French.
- Return strict JSON only when requested.
- Focus on identity consistency, duplication risk, document completeness and legal age constraints.
- Do not provide legal decisions, only risk analysis and reasons.
- Risk levels must be one of: low, medium, high.
PROMPT;
    }

    public function messages(): iterable
    {
        return [];
    }
}
