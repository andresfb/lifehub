<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Jobs;

use App\Domain\Bookmarks\Dtos\BulkMarkerImportItem;
use App\Domain\Bookmarks\Services\BulkMarkerImportService;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class BulkMarkerImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  Collection<BulkMarkerImportItem>  $markers
     */
    public function __construct(private readonly Collection $markers)
    {
        $this->queue = 'import';
        $this->delay = now()->addSeconds(5);
    }

    public function handle(BulkMarkerImportService $service): void
    {
        try {
            Auth::setUser(User::getAdmin());

            $service->execute($this->markers);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
