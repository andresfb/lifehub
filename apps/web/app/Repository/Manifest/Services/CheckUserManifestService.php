<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Models\ApiManifest;
use Illuminate\Support\Facades\Cache;
use Throwable;

final readonly class CheckUserManifestService
{
    public function __construct(
        private ApiManifestService $manifestService
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(int $userId): void
    {
        try {
            $manifest = ApiManifest::getUserNavigation($userId);
            if (! $manifest instanceof ApiManifest) {
                $this->manifestService->loadUserManifest($userId);

                return;
            }

            $version = $this->manifestService->getVersion($userId);
            if ($manifest->version === $version) {
                return;
            }

            $this->manifestService->loadUserManifest($userId);
        } finally {
            Cache::put(
                md5("USER:MANIFEST:VERSION:{$userId}"),
                $userId,
                now()->addHours(8)
            );
        }
    }
}
