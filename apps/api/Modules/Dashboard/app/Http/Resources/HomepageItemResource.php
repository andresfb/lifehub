<?php

namespace Modules\Dashboard\Http\Resources;

use Modules\Dashboard\Dtos\HomepageItemDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HomepageItemDto
 */
class HomepageItemResource extends JsonResource
{
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
