<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services\Pins;

use App\Libraries\ApiLibrary;
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
use LifeHub\ApiClient\Model\HomepageItemResource;
use LifeHub\ApiClient\Model\HomepageSectionResource;
use LifeHub\ApiClient\Model\V1DashboardPinsIndex200Response;
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
        $response = ApiLibrary::pinApi($userId)
            ->v1DashboardPinsIndex(
                status: $status->value,
                include: 'items',
            );

        if (! $response instanceof V1DashboardPinsIndex200Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->isNullableSetToNull('data')) {
            return [];
        }

        $included = collect($response->getIncluded() ?? []);

        return collect($response->getData())
            ->map(function (HomepageSectionResource $section) use ($included): array {
                $relationships = collect($section->getRelationships()?->getItems()?->getData() ?: []);
                $itemIds = $relationships->pluck('id')->toArray();
                $items = $included->whereIn('id', $itemIds);

                return [
                    'id' => $section->getId(),
                    'slug' => $section->getAttributes()->getSlug(),
                    'name' => $section->getAttributes()->getName(),
                    'order' => $section->getAttributes()->getOrder(),
                    'items' => $items->map(function (HomepageItemResource $item) use ($section): array {
                        return [
                            'id' => $item->getId(),
                            'slug' => $item->getAttributes()->getSlug(),
                            'title' => $item->getAttributes()->getTitle(),
                            'description' => $item->getAttributes()->getDescription(),
                            'url' => $item->getAttributes()->getUrl(),
                            'active' => $item->getAttributes()->getActive(),
                            'order' => $item->getAttributes()->getOrder(),
                            'icon' => $item->getAttributes()->getIcon(),
                            'icon_color' => $item->getAttributes()->getIconColor(),
                            'section_slug' => $section->getAttributes()->getSlug(),
                            'tags' => $item->getAttributes()->getTags(),
                        ];
                    })->all(),
                ];
            })
            ->all();
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
