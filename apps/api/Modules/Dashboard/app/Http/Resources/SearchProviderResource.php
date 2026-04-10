<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Override;

/**
 * @mixin SearchProviderItem
 */
final class SearchProviderResource extends JsonResource
{
    #[Override]
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
            ],
        ];
    }
}
