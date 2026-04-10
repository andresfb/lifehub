<?php

namespace Modules\Dashboard\Http\Resources;

use App\Dtos\Modules\ModuleRecordItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ModuleRecordItem
 */
class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->key,
            'type' => 'module',
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'is_core' => $this->isCore,
                'is_public' => $this->isPublic,
                'status' => $this->status,
                'show_menu' => $this->showMenu,
            ],
            'relationships' => [
                'menu' => $this->whenNotNull(
                    $this->menu,
                    MenuResource::make($this->menu),
                ),
                'sub_menus' => MenuResource::collection(
                    $this->whenNotNull($this->subMenus)
                ),
            ]
        ];
    }
}
