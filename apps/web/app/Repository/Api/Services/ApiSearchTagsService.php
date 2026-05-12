<?php

declare(strict_types=1);

namespace App\Repository\Api\Services;

use App\Repository\Api\Dtos\TagItem;
use App\Repository\Api\Libraries\ApiLibrary;
use Exception;
use Illuminate\Support\Collection;
use LifeHub\ApiClient\Model\TagResource;
use LifeHub\ApiClient\Model\V1SearchTags200Response;
use RuntimeException;

final readonly class ApiSearchTagsService
{
    /**
     * @return Collection<string, TagItem>
     *
     * @throws Exception
     */
    public function getUserTags(int $userId, string $query): Collection
    {
        $response = ApiLibrary::searchTagApi($userId)
            ->v1SearchTags($query);

        if (! $response instanceof V1SearchTags200Response) {
            $message = $response->isNullableSetToNull('errors')
                ? $response->getMessage()
                : $response->getErrors();

            throw new RuntimeException($message);
        }

        $payload = $response->getData();

        return collect($payload)
            ->map(function (TagResource $tag): TagItem {
                return TagItem::from([
                    'id' => $tag->getId(),
                    'slug' => $tag->getAttributes()->getSlug(),
                    'name' => $tag->getAttributes()->getName(),
                ]);
            });
    }
}
