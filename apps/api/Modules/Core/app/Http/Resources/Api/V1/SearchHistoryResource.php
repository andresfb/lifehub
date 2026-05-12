<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Api\V1;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Core\Models\SearchHistory;

/** @mixin SearchHistory */
final class SearchHistoryResource extends JsonApiResource
{
    /** @var array<int, string> */
    public array $attributes = [
        'module',
        'type',
        'query',
        'hash',
        'created_at',
        'updated_at',
    ];
}
