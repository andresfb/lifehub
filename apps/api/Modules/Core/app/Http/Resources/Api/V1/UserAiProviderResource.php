<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Core\Models\AiProvider;
use Override;

/**
 * @mixin AiProvider
 */
final class UserAiProviderResource extends JsonApiResource
{
    #[Override]
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    #[Override]
    public function toType(Request $request): string
    {
        return 'ai-provider';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'has_api_key' => filled($this->api_key),
            'url' => $this->url,
            'api_version' => $this->api_version,
            'deployment' => $this->deployment,
            'embedding_deployment' => $this->embedding_deployment,
            'models' => $this->whenLoaded('models', fn () => $this->models
                ->map(fn ($model): array => new UserAiModelResource($model)->resolve()['data'])
                ->values()
                ->all(), []),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
