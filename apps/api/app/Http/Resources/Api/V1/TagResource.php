<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Tag;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

/** @mixin Tag */
final class TagResource extends JsonApiResource
{
    /** @var array<int, string> */
    public array $attributes = [
        'slug',
        'name',
    ];
}
