<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Modules\Core\Models\SearchHistory */
final class SearchHistoryCollection extends ResourceCollection
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Success',
            'data' => $this->collection,
        ];
    }
}
