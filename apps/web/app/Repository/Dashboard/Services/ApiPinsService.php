<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services;

use App\Repository\Common\Libraries\ApiClient;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use Throwable;

final readonly class ApiPinsService
{
    public function __construct(
        private ApiClient $apiClient,
    ) {}

    /**
     * @throws Throwable
     */
    public function getUserPins(int $userId): array
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::DASHBOARD,
            ManifestAction::LIST,
            ManifestMethod::GET,
        );

        return $this->apiClient
            ->setUserId($userId)
            ->request($endpoint->method, $endpoint->getUri());
    }
}
