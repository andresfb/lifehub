<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Services;

use App\Domain\Bookmarks\Models\Marker;
use App\Domain\Bookmarks\Tasks\MarkerAiSummaryTask;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

final readonly class MarkerAiSummaryService
{
    public function __construct(
        private MarkerAiSummaryTask $task
    ) {}

    public function execute(Marker $marker): void
    {
        [$summary, $tags] = $this->getSummary($marker);

        $marker->summary = $summary;
        $marker->saveQuietly();
        Cache::tags('markers')->flush();

        if (! is_array($tags) || blank($tags)) {
            return;
        }

        $tags = collect($tags)
            ->map(function (string $tag) {
                return str($tag)
                    ->trim()
                    ->lower()
                    ->toString();
            });

        $marker->fresh()->attachTags($tags);
    }

    private function getSummary(Marker $marker): array
    {
        [$found, $summary, $tags] = $this->findSimilar($marker);
        if ($found) {
            return [$summary, $tags];
        }

        $response = $this->task
            ->setModel($marker)
            ->handle();

        if (blank($response['summary'])) {
            throw new RuntimeException('The Agent did not generate an summary');
        }

        return [$response['summary'], $response['tags']];
    }

    private function findSimilar(Marker $marker): array
    {
        $marker = Marker::query()
            ->with('tags')
            ->where('url', $marker->url)
            ->where('id', '!=', $marker->id)
            ->whereNotNull('summary')
            ->first();

        return [
            filled($marker),
            $marker->summary ?? null,
            $marker?->getTags(),
        ];
    }
}
