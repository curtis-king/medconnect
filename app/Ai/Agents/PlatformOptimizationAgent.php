<?php

namespace App\Ai\Agents;

use App\Ai\Tools\PlatformMetricsTool;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class PlatformOptimizationAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are a healthcare platform optimization copilot.

Your task is to produce actionable operational recommendations based on real system metrics.
Always call the `PlatformMetricsTool` first before providing recommendations.

Output rules:
- Write in French.
- Keep output concise and practical.
- Prioritize actions by severity: Critique, Important, Opportunite.
- Include explicit numeric evidence from tool output in each recommendation.
- End with a short execution checklist for operations teams.
PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new PlatformMetricsTool,
        ];
    }
}
