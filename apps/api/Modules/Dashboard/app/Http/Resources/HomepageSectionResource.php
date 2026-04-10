<?php

namespace Modules\Dashboard\Http\Resources;

use Modules\Dashboard\Dtos\HomepageSectionItem;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HomepageSectionItem
 */
class HomepageSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'item',
            'attributes' => [
                'userId' => $this->userId,
                'slug' => $this->slug,
                'name' => $this->name,
                'bg_color' => $this->bgColor ?? '',
            ],
            'relationships' => [
                'items' => HomepageItemResource::collection(
                    $this->whenLoaded('items')
                ),
                'user' => UserResource::make($this->whenLoaded('user')),
            ]
        ];
    }
}
