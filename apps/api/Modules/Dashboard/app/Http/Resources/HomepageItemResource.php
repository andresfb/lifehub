<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Dtos\HomepageItemDto;
use Override;

/**
 * @mixin HomepageItemDto
 */
final class HomepageItemResource extends JsonApiResource
{
    #[Override]
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    #[Override]
    public function toType(Request $request): string
    {
        return 'homepage-items';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'url' => $this->url,
            'color' => $this->color,
            'description' => $this->description ?? '',
            'image' => $this->image ?? '',
            'tags' => $this->tags ?? [],
        ];
    }
}
