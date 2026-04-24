<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Models\ApiManifest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;
use RuntimeException;
use Throwable;

final readonly class ManifestService
{
    public function __construct(
        private ApiManifestService $apiService
    ) {}

    /**
     * @throws Throwable
     */
    public function getForUser(int $userId): array
    {
        $cached = Cache::tags(['manifest'])
            ->remember(
                md5("manifest:{$userId}"),
                now()->addWeek(),
                function () use ($userId): array {
                    $manifest = ApiManifest::getForUser($userId);
                    if ($manifest instanceof ApiManifest) {
                        return $manifest->payload;
                    }

                    return $this->apiService->loadUserManifest($userId)['modules'];
                }
            );

        if (blank($cached)) {
            throw new RuntimeException('No API Manifest found');
        }

        Concurrency::defer([
            fn () => ApiManifestService::checkVersion($userId),
        ]);

        return $cached;
    }
}
