<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Models\HomepageSection;

/**
 * @mixin HomepageSection
 */
final class HomepageSectionResource extends JsonApiResource
{
    public array $attributes = [
        'user_id',
        'slug',
        'name',
        'order',
    ];

    public array $relationships = [
        'user',
        'items',
    ];
}
