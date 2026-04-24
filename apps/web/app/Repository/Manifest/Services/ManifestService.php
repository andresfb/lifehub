<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Dtos\Manifest\ModuleItem;
use App\Models\ApiManifest;
use App\Models\ApiManifestModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;
use Throwable;

final readonly class ManifestService
{
    public function __construct(
        private ApiManifestService $apiService
    ) {}

    /**
     * @return Collection<ApiManifestModule>
     *
     * @throws Throwable
     */
    public function getUserNavigation(int $userId): Collection
    {
        $cached = Cache::tags(['manifest'])
            ->remember(
                md5("navigation:{$userId}"),
                now()->addWeek(),
                function () use ($userId): array {
                    $manifest = $this->loadNavigation($userId);
                    if (filled($manifest)) {
                        return $manifest;
                    }

                    $this->apiService->loadUserManifest($userId);

                    return $this->loadNavigation($userId);
                });

        Concurrency::defer([
            fn () => ApiManifestService::checkVersion($userId),
        ]);

        if (blank($cached)) {
            return collect();
        }

        return collect($cached)
            ->map(fn (array $node) => ModuleItem::from($node));
    }

    /**
     * @return array<int, ApiManifestModule>|null
     */
    private function loadNavigation(int $userId): ?array
    {
        $manifest = ApiManifest::getUserNavigation($userId);
        if (! $manifest instanceof ApiManifest) {
            return null;
        }

        return $manifest->modules->toArray();
    }
}
