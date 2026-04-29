<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dashboard\Models\HomepageItem;

/**
 * @mixin HomepageItem
 */
final class HomepageItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'url' => $this->url,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,
            'description' => $this->description ?? '',
            'order' => $this->order,
            'tags' => $this->getTags(),
        ];
    }
}
