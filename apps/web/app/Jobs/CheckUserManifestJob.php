<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repository\Manifest\Services\CheckUserManifestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class CheckUserManifestJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $userId) {}

    public function uniqueId(): string
    {
        return "check-user-manifest-{$this->userId}";
    }

    /**
     * @throws Throwable
     */
    public function handle(CheckUserManifestService $service): void
    {
        $service->execute($this->userId);
    }
}
