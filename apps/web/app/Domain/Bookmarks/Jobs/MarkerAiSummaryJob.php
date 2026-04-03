<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Jobs;

use App\Domain\Bookmarks\Models\Marker;
use App\Domain\Bookmarks\Services\MarkerAiSummaryService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class MarkerAiSummaryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $markerId)
    {
        $this->queue = 'ai';
        $this->delay = now()->addSeconds(10);
    }

    public function handle(MarkerAiSummaryService $service): void
    {
        try {
            $marker = Marker::query()
                ->withoutGlobalScopes()
                ->with('user')
                ->where('id', $this->markerId)
                ->firstOrFail();

            Auth::setUser($marker->user);

            $service->execute($marker);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            SearchDocumentUpdatedJob::dispatch($this->markerId);
        }
    }
}
