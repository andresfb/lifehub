<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \Modules\Dashboard\Models\SearchProvider */
final class SearchProviderCollection extends ResourceCollection
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
