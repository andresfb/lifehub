<?php

declare(strict_types=1);

namespace App\Repository\Core\Services\SearchHistory;

use App\Repository\Common\Libraries\ApiClient;
use App\Repository\Core\Dtos\SearchHistory\SearchTermItem;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use Exception;
use RuntimeException;

final readonly class ApiSearchHistoryService
{
    public function __construct(
        private ApiClient $apiClient,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function listTerms(int $userId, SearchTermItem $item): array
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::CORE,
            ManifestActionOwner::SEARCH_TERNS,
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
                $item->toArray(),
            );
    }

    /**
     * @throws Exception
     */
    public function saveTerm(int $userId, SearchTermItem $item): void
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::CORE,
            ManifestActionOwner::SEARCH_TERNS,
            ManifestAction::SAVE,
            ManifestMethod::POST,
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
    }
}
