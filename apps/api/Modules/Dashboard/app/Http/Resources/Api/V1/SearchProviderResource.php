<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Models\SearchProvider;

/**
 * @mixin SearchProvider
 */
final class SearchProviderResource extends JsonApiResource
{
    /** @var array<int, string> */
    public array $attributes = [
        'name',
        'url',
        'icon',
        'icon_color',
        'order',
        'default',
        'created_at',
        'updated_at',
    ];
}
