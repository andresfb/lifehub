<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dashboard\Dtos\HomepageItemDto;
use Override;

/**
 * @mixin HomepageItemDto
 */
final class HomepageItemResource extends JsonResource
{
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'section',
            'attributes' => [
                'slug' => $this->slug,
                'title' => $this->title,
                'url' => $this->url,
                'image' => $this->image ?? '',
                'tags' => $this->tags ?? [],
            ],
        ];
    }
}
