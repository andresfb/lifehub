<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dashboard\Models\SearchProvider;

/**
 * @mixin SearchProvider
 */
final class SearchProviderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon ?? '',
            'icon_color' => $this->icon_color ?? '',
            'order' => $this->order,
            'default' => $this->default,
        ];
    }
}
