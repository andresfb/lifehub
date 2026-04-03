<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

final class MarkerAiSummaryAgent implements Agent, HasStructuredOutput, HasTools
{
    use Promptable;

    public function instructions(): string
    {
        return Config::string('markers.summary_prompt');
    }

    public function tools(): iterable
    {
        return [];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'summary' => $schema->string()->required(),
            'tags' => $schema->array()
                ->items($schema->string())
                ->required(),
        ];
    }
}
