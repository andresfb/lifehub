<?php

namespace App\Domain\Dashboard\Http\Resources;

use App\Domain\Dashboard\Dtos\HomepageSectionItem;
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
                'bgColor' => $this->bgColor ?? '',
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
