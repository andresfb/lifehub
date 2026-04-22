<?php

namespace App\Repository\Manifest\Services;

use App\Models\ApiCatalog;
use Throwable;

readonly class CheckUserManifestService
{
    public function __construct(
        private ApiManifestService $manifestService
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(int $userId): void
    {
        $catalog = ApiCatalog::getUserCatalog($userId);
        if (! $catalog instanceof ApiCatalog) {
            $this->manifestService->loadUserManifest($userId);

            return;
        }

        $version = $this->manifestService->getVersion();
        if ($catalog->version === $version) {
            return;
        }

        $this->manifestService->loadUserManifest($userId);
    }
}
