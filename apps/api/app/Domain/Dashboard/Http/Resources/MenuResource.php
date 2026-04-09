<?php

namespace App\Domain\Dashboard\Http\Resources;

use App\Dtos\Modules\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MenuItem
 */
class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->code,
            'type' => 'menu',
            'attributes' => [
                'title' => $this->title,
                'routes' => $this->routes,
                'icon' => $this->icon ?? '',
                'short_cut' => $this->shortCut ?? '',
            ],
        ];
    }
}
