<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dashboard\Dtos\HomepageSectionItem;
use Override;

/**
 * @mixin HomepageSectionItem
 */
final class HomepageSectionResource extends JsonResource
{
    #[Override]
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
            ],
        ];
    }
}
