<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Illuminate\Support\Collection;
use Modules\Dashboard\Dtos\HomepageItemDto;
use Modules\Dashboard\Dtos\HomepageSectionItem;
use Override;

/**
 * @mixin HomepageSectionItem
 */
final class HomepageSectionResource extends JsonApiResource
{
    #[Override]
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    #[Override]
    public function toType(Request $request): string
    {
        return 'homepage-sections';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'userId' => $this->userId,
            'slug' => $this->slug,
            'name' => $this->name,
            'order' => $this->order,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toRelationships(Request $request): array
    {
        /** @var Collection<int, HomepageItemDto> $items */
        $items = $this->items ?? collect();

        return [
            'items' => HomepageItemResource::collection($items)->resolve($request),
        ];
    }

    #[Override]
    public function resolveResourceData(Request $request): array
    {
        $data = parent::resolveResourceData($request);
        $relationships = $this->toRelationships($request);

        if ($relationships === []) {
            return $data;
        }

        $data['relationships'] = (object) $relationships;

        return $data;
    }
}
