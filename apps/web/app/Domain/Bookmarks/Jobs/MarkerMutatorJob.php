<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Jobs;

use App\Domain\Bookmarks\Models\Marker;
use App\Domain\Core\Services\MarkerMutatorService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class MarkerMutatorJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $markerId,
    ) {}

    public function handle(MarkerMutatorService $service): void
    {
        try {
            $marker = Marker::query()
                ->where('id', $this->markerId)
                ->firstOrFail();

            $service->execute($marker);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
