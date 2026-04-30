<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services;

use App\Repository\Common\Libraries\ApiClient;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use RuntimeException;
use Throwable;

final readonly class ApiSearchProviderService
{
    public function __construct(
        private ApiClient $apiClient,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    public function getProviders(int $userId): array
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::DASHBOARD,
            ManifestActionOwner::SEARCH,
            ManifestAction::LIST,
            ManifestMethod::GET,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        return $this->apiClient
            ->setUserId($userId)
            ->request($endpoint->method, $endpoint->getUri());
    }
}
