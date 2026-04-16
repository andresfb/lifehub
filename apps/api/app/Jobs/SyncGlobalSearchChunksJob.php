<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Search\SyncGlobalSearchChunksService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SyncGlobalSearchChunksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $globalSearchId)
    {
        $this->queue = 'ai';
        $this->delay = now()->addSeconds(15);
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 60, 180];
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping("global-search-chunks-{$this->globalSearchId}")
                ->releaseAfter(30)
                ->expireAfter(300),
        ];
    }

    /**
     * @throws Throwable
     */
    public function handle(SyncGlobalSearchChunksService $service): void
    {
        try {
            $service->execute($this->globalSearchId);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
