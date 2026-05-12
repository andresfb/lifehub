<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Models\HomepageSection;

/**
 * @mixin HomepageSection
 */
final class HomepageSectionResource extends JsonApiResource
{
    /** @var array<int, string> */
    public array $attributes = [
        'slug',
        'name',
        'order',
        'created_at',
        'updated_at',
    ];

    /** @var array<string, class-string<HomepageItemResource>> */
    public array $relationships = [
        'items' => HomepageItemResource::class,
    ];
}
