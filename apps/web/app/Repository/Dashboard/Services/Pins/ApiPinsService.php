<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services\Pins;

use App\Repository\Api\Libraries\ApiLibrary;
use App\Repository\Dashboard\Dtos\Pins\PinCreateItem;
use App\Repository\Dashboard\Dtos\Pins\PinUpdateItem;
use App\Repository\Dashboard\Enums\PinStatus;
use Exception;
use Illuminate\Support\Facades\Cache;
use LifeHub\ApiClient\Model\HomepageItemResource;
use LifeHub\ApiClient\Model\HomepageSectionResource;
use LifeHub\ApiClient\Model\PinCreateRequest;
use LifeHub\ApiClient\Model\PinUpdateRequest;
use LifeHub\ApiClient\Model\V1DashboardPinsIndex200Response;
use LifeHub\ApiClient\Model\V1DashboardPinsStore201Response;
use LifeHub\ApiClient\Model\V1DashboardPinsUpdate200Response;
use RuntimeException;
use Throwable;

final readonly class ApiPinsService
{
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
        $request = new PinCreateRequest($item->toArray());

        $response = ApiLibrary::pinApi($userId)
            ->v1DashboardPinsStore($request);

        if (! $response instanceof V1DashboardPinsStore201Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->getSuccess() !== true) {
            throw new RuntimeException($response->getMessage());
        }

        Cache::tags(['pins'])->flush();
    }

    /**
     * @throws Exception
     */
    public function updatePin(int $userId, string $pinSlug, PinUpdateItem $item): void
    {
        $request = new PinUpdateRequest($item->toArray());

        $response = ApiLibrary::pinApi($userId)
            ->v1DashboardPinsUpdate($pinSlug, $request);

        if (! $response instanceof V1DashboardPinsUpdate200Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->getSuccess() !== true) {
            throw new RuntimeException($response->getMessage());
        }

        Cache::tags(['pins'])->flush();
    }

    /**
     * @throws Exception
     */
    public function deletePin(int $userId, string $pinSlug): void
    {
        ApiLibrary::pinApi($userId)
            ->v1DashboardPinsDestroy($pinSlug);

        Cache::tags(['pins'])->flush();
    }
}
