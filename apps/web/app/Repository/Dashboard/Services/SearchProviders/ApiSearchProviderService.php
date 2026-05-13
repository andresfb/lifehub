<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services\SearchProviders;

use App\Repository\Api\Libraries\ApiLibrary;
use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderCreateItem;
use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderUpdateItem;
use Exception;
use Illuminate\Support\Facades\Cache;
use LifeHub\ApiClient\Model\SearchProviderCreateRequest;
use LifeHub\ApiClient\Model\SearchProviderResource;
use LifeHub\ApiClient\Model\SearchProviderUpdateRequest;
use LifeHub\ApiClient\Model\V1DashboardSearchProvidersIndex200Response;
use LifeHub\ApiClient\Model\V1DashboardSearchProvidersStore201Response;
use RuntimeException;
use Throwable;

final readonly class ApiSearchProviderService
{
    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    public function getProviders(int $userId): array
    {
        $response = ApiLibrary::searchProviderApi($userId)
            ->v1DashboardSearchProvidersIndex();

        if (! $response instanceof V1DashboardSearchProvidersIndex200Response) {
            throw new RuntimeException($response->getMessage());
        }

        if ($response->isNullableSetToNull('data')) {
            return [];
        }

        return collect($response->getData())
            ->map(function (SearchProviderResource $provider): array {
                return [
                    'id' => $provider->getId(),
                    'name' => $provider->getAttributes()->getName(),
                    'url' => $provider->getAttributes()->getUrl(),
                    'default' => $provider->getAttributes()->getDefault(),
                    'order' => $provider->getAttributes()->getOrder(),
                    'icon' => $provider->getAttributes()->getIcon(),
                    'icon_color' => $provider->getAttributes()->getIconColor(),
                ];
            })->all();
    }

    /**
     * @throws Exception
     */
    public function createProvider(int $userId, SearchProviderCreateItem $item): void
    {
        $request = new SearchProviderCreateRequest($item->toArray());

        $response = ApiLibrary::searchProviderApi($userId)
            ->v1DashboardSearchProvidersStore($request);

        if (! $response instanceof V1DashboardSearchProvidersStore201Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->getSuccess() !== true) {
            throw new RuntimeException($response->getMessage());
        }

        Cache::tags(['search-providers'])->flush();
    }

    /**
     * @throws Exception
     */
    public function updateProvider(int $userId, string $providerSlug, SearchProviderUpdateItem $item): void
    {
        $request = new SearchProviderUpdateRequest($item->toArray());

        $response = ApiLibrary::searchProviderApi($userId)
            ->v1DashboardSearchProvidersUpdate($providerSlug, $request);

        if (! $response instanceof V1DashboardSearchProvidersStore201Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->getSuccess() !== true) {
            throw new RuntimeException($response->getMessage());
        }

        Cache::tags(['search-providers'])->flush();
    }

    /**
     * @throws Exception
     */
    public function deleteProvider(int $userId, string $providerSlug): void
    {
        ApiLibrary::searchProviderApi($userId)
            ->v1DashboardSearchProvidersDestroy($providerSlug);

        Cache::tags(['search-providers'])->flush();
    }
}
