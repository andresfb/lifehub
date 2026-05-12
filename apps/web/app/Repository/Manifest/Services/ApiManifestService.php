<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Jobs\CheckUserManifestJob;
use App\Repository\Api\Libraries\ApiLibrary;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LifeHub\ApiClient\Model\V1ManifestoVersion200Response;
use LifeHub\ApiClient\Model\V1Search200Response;
use RuntimeException;
use Throwable;

final readonly class ApiManifestService
{
    public function __construct(
        private ImportCatalogService $catalogService,
    ) {}

    public static function checkVersion(int $userId): void
    {
        if (Cache::has(md5("USER:MANIFEST:VERSION:{$userId}"))) {
            return;
        }

        CheckUserManifestJob::dispatch($userId);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    public function loadUserManifest(int $userId): array
    {
        Cache::tags(['manifest'])->flush();
        Cache::forget(md5("USER:MANIFEST:VERSION:{$userId}"));

        $response = ApiLibrary::manifestApi($userId)
            ->v1Manifesto();

        if (! $response instanceof V1Search200Response) {
            throw new RuntimeException($response->getMessage());
        }

        $payload = $response->getData();
        $this->catalogService->execute($payload, $userId);

        return $payload;
    }

    public function getVersion(int $userId): ?string
    {
        try {
            $response = ApiLibrary::manifestApi($userId)
                ->v1ManifestoVersion();

            if (! $response instanceof V1ManifestoVersion200Response) {
                throw new RuntimeException($response->getMessage());
            }

            return $response->getData()->getVersion();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
