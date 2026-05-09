<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Models\SearchHistory;

/** @mixin SearchHistory */
final class SearchHistoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'type' => $this->type,
            'query' => $this->query,
            'hash' => $this->hash,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_id' => $this->user_id,

            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
