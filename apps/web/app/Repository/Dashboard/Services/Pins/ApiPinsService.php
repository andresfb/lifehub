<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services\Pins;

use App\Repository\Common\Libraries\ApiClient;
use App\Repository\Dashboard\Dtos\Pins\PinCreateItem;
use App\Repository\Dashboard\Dtos\Pins\PinUpdateItem;
use App\Repository\Dashboard\Enums\PinStatus;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use Exception;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;

final readonly class ApiPinsService
{
    public function __construct(
        private ApiClient $apiClient,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    public function getUserPins(int $userId, PinStatus $status): array
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::DASHBOARD,
            ManifestActionOwner::PINS,
            ManifestAction::LIST,
            ManifestMethod::GET,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        return $this->apiClient
            ->setUserId($userId)
            ->request(
                $endpoint->method,
                $endpoint->getUri(),
                ['status' => $status->value]
            );
    }

    /**
     * @throws Exception
     */
    public function createPin(int $userId, PinCreateItem $item): void
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            userId: $userId,
            module: ManifestModule::DASHBOARD,
            owner: ManifestActionOwner::PINS,
            action: ManifestAction::SAVE,
            method: ManifestMethod::POST,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        $this->apiClient
            ->setUserId($userId)
            ->request(
                $endpoint->method,
                $endpoint->getUri(),
                $item->toArray(),
            );

        Cache::tags(['pins'])->flush();
    }

    /**
     * @throws Exception
     */
    public function updatePin(int $userId, string $pinSlug, PinUpdateItem $item): void
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            userId: $userId,
            module: ManifestModule::DASHBOARD,
            owner: ManifestActionOwner::PINS,
            action: ManifestAction::UPDATE,
            method: ManifestMethod::PUT,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        $this->apiClient
            ->setUserId($userId)
            ->request(
                $endpoint->method,
                $endpoint->getUri($pinSlug),
                $item->toArray(),
            );

        Cache::tags(['pins'])->flush();
    }

    /**
     * @throws Exception
     */
    public function deletePin(int $userId, string $pinSlug): void
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            userId: $userId,
            module: ManifestModule::DASHBOARD,
            owner: ManifestActionOwner::PINS,
            action: ManifestAction::DELETE,
            method: ManifestMethod::DELETE,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        $this->apiClient
            ->setUserId($userId)
            ->request(
                $endpoint->method,
                $endpoint->getUri($pinSlug),
            );

        Cache::tags(['pins'])->flush();
    }
}
