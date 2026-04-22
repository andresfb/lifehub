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
    public function loadUserManifest(int $userId): void
    {
        $payload = $this->apiClient->get(
            Config::string('services.backend.endpoints.manifest.data'),
        );

        $this->catalogService->execute($payload, $userId);
    }

    public function getVersion(): ?string
    {
        try {
            $payload = $this->apiClient->get(
                Config::string('services.backend.endpoints.manifest.version'),
            );

            return $payload['version'] ?? null;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
