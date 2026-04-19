<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Models\HomepageItem;
use Override;

/**
 * @mixin HomepageItem
 */
final class HomepageItemResource extends JsonApiResource
{
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
            'bg_color' => $this->bg_color,
            'description' => $this->description ?? '',
            'image' => $this->getIcon(),
            'tags' => $this->getTags(),
        ];
    }
}
