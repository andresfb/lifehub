<?php

declare(strict_types=1);

namespace App\Repository\Common\Services;

use App\Repository\Common\Dtos\TagItem;
use App\Repository\Common\Libraries\ApiClient;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;

final readonly class ApiSearchTagsService
{
    public function __construct(
        private ApiClient $apiClient,
    ) {}

    /**
     * @return Collection<string, TagItem>
     * @throws Exception
     */
    public function getUseTags(int $userId, string $query): Collection
    {
        $endpoint = ManifestActionsLibrary::getEndpoint(
            $userId,
            ManifestModule::CORE,
            ManifestActionOwner::SEARCH_TAGS,
            ManifestAction::SEARCH,
            ManifestMethod::GET,
        );

        if (blank($endpoint)) {
            throw new RuntimeException('Endpoint not found');
        }

        $result = $this->apiClient
            ->setUserId($userId)
            ->request(
                $endpoint->method,
                $endpoint->getUri(),
                ['q' => mb_trim($query)],
            );

        if (blank($result)) {
            return collect();
        }

        return collect($result)
            ->map(fn (array $tag): TagItem => TagItem::from($tag));
    }
}
