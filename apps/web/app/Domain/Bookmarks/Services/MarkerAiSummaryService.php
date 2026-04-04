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
        $response = $this->task
            ->setModel($marker)
            ->handle();

        if (blank($response['summary'])) {
            throw new RuntimeException('The Agent did not generate an summary');
        }

        $marker->summary = $response['summary'];
        $marker->saveQuietly();
        Cache::tags('markers')->flush();

        if (! is_array($response['tags']) || blank($response['tags'])) {
            return;
        }

        $tags = collect($response['tags'])
            ->map(function (string $tag) {
                return str($tag)
                    ->trim()
                    ->lower()
                    ->toString();
            });

        $marker->fresh()->attachTags($tags);
    }
}
