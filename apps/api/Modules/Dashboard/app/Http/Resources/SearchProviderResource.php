<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Override;

/**
 * @mixin SearchProviderItem
 */
final class SearchProviderResource extends JsonApiResource
{
    #[Override]
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    #[Override]
    public function toType(Request $request): string
    {
        return 'search-provider';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'order' => $this->order,
            'default' => $this->default,
        ];
    }
}
