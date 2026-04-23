<?php

namespace App\Repository\Manifest\Services;

use App\Jobs\CheckUserManifestJob;
use App\Repository\Common\Libraries\ApiClient;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ApiManifestService
{
    public function __construct(
        private ApiClient $apiClient,
        private ImportApiCatalogService $catalogService,
    ) {}

    public static function checkVersion(int $userId): void
    {
        if (Cache::has(md5("USER:MANIFEST:VERSION:$userId"))) {
            return;
        }

        CheckUserManifestJob::dispatch($userId);
    }

    /**
     * @throws Throwable
     */
    public function loadUserManifest(int $userId): array
    {
        $payload = $this->apiClient
            ->setUserId($userId)
            ->get(
                uri: Config::string('services.backend.endpoints.manifest.data'),
            );

        $this->catalogService->execute($payload, $userId);

        return $payload;
    }

    public function getVersion(int $userId): ?string
    {
        try {
            $payload = $this->apiClient
                ->setUserId($userId)
                ->get(
                  uri: Config::string('services.backend.endpoints.manifest.version'),
                );

            return $payload['version'] ?? null;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
