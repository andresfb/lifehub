<?php

namespace App\Jobs;

use App\Repository\Manifest\Services\CheckUserManifestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CheckUserManifestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $userId) {}

    public function middleware(): array
    {
        return [
            new WithoutOverlapping("check-user-manifest-{$this->userId}")
                ->dontRelease(),
        ];
    }

    /**
     * @throws Throwable
     */
    public function handle(CheckUserManifestService $service): void
    {
        $service->execute($this->userId);
    }
}
