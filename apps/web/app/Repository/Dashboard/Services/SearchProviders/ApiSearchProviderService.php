<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Services\SearchProviders;

use App\Repository\Api\Libraries\ApiLibrary;
use LifeHub\ApiClient\Model\SearchProviderResource;
use LifeHub\ApiClient\Model\V1DashboardSearchProvidersIndex200Response;
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
}
