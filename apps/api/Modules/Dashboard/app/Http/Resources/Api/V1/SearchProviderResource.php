<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Dashboard\Models\SearchProvider;
use Override;

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

    #[Override]
    public function toAttributes(Request $request): array
    {
        $url = str($this->url)
            ->replace("?{$this->term_field}=", "?{$this->term_field}=%s")
            ->replace("&{$this->term_field}=", "&{$this->term_field}=%s")
            ->value();

        return [
            'name' => $this->name,
            'url' => $url,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,
            'order' => $this->order,
            'term_field' => $this->term_field,
            'default' => $this->default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
