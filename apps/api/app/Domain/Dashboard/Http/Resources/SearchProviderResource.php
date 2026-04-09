<?php

namespace App\Domain\Dashboard\Http\Resources;

use App\Domain\Dashboard\Dtos\SearchProviderItem;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SearchProviderItem
 */
class SearchProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'provider',
            'attributes' => [
                'name' => $this->name,
                'url' => $this->url,
                'order' => $this->order,
            ],
            'relationships' => [
                'user' => UserResource::make($this->whenLoaded('user')),
            ]
        ];
    }
}
