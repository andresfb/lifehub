<?php

namespace App\Domain\Bookmarks\Jobs;

use App\Domain\Bookmarks\Models\Marker;
use App\Dtos\Media\PageScreenshotItem;
use App\Services\Media\CaptureScreenshotService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MarkerScreenshotJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly PageScreenshotItem $item,
    ) {
        $this->queue = 'media';
        $this->delay = now()->addSeconds(5);
    }

    public function handle(CaptureScreenshotService $service): void
    {
        try {
            if (! Config::boolean('constants.browsershot_fallback')) {
                return;
            }

            $marker = Marker::query()
                ->withoutGlobalScopes()
                ->with('user')
                ->with('media')
                ->where('id', $this->item->modelId)
                ->firstOrFail();

            Auth::setUser($marker->user);

            $service->execute($this->item, $marker);
        } catch (Exception $e) {
            Log::error(sprintf(
                "Screenshot capture failed for Model: %s, %s: %s",
                $this->item->modelId,
                $this->item->url,
                $e->getMessage())
            );
        }
    }
}
