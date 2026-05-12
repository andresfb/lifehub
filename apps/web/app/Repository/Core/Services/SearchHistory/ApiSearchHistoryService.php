<?php

declare(strict_types=1);

namespace App\Repository\Core\Services\SearchHistory;

use App\Repository\Api\Libraries\ApiLibrary;
use App\Repository\Core\Dtos\SearchHistory\SearchTermItem;
use Exception;
use LifeHub\ApiClient\Model\SearchHistoryCreateRequest;
use LifeHub\ApiClient\Model\SearchHistoryResource;
use LifeHub\ApiClient\Model\V1SearchHistoryStore201Response;
use LifeHub\ApiClient\Model\V1SearchTerms200Response;
use RuntimeException;

final readonly class ApiSearchHistoryService
{
    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function listTerms(int $userId, SearchTermItem $item): array
    {
        $response = ApiLibrary::searchHistoryApi($userId)
            ->v1SearchTerms(
                module: $item->module,
                type: $item->type,
                term: $item->term,
            );

        if (! $response instanceof V1SearchTerms200Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->isNullableSetToNull('data')) {
            return [];
        }

        return collect($response->getData())
            ->map(function (SearchHistoryResource $item): array {
                return [
                    'module' => $item->getAttributes()->getModule(),
                    'type' => $item->getAttributes()->getType(),
                    'term' => $item->getAttributes()->getQuery(),
                ];
            })->all();
    }

    /**
     * @throws Exception
     */
    public function saveTerm(int $userId, SearchTermItem $item): void
    {
        $request = new SearchHistoryCreateRequest($item->toArray());

        $response = ApiLibrary::searchHistoryApi($userId)
            ->v1SearchHistoryStore($request);

        if (! $response instanceof V1SearchHistoryStore201Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        if ($response->getSuccess() !== true) {
            throw new RuntimeException($response->getMessage());
        }
    }
}
