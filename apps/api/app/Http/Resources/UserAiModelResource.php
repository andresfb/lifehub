<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AiModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Override;

/**
 * @mixin AiModel
 */
final class UserAiModelResource extends JsonApiResource
{
    #[Override]
    public function toId(Request $request): string
    {
        return (string) $this->id;
    }

    #[Override]
    public function toType(Request $request): string
    {
        return 'ai-model';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toAttributes(Request $request): array
    {
        return [
            'provider_id' => $this->ai_provider_id,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'supports_text' => $this->supports_text,
            'supports_images' => $this->supports_images,
            'supports_tts' => $this->supports_tts,
            'supports_stt' => $this->supports_stt,
            'supports_embeddings' => $this->supports_embeddings,
            'supports_reranking' => $this->supports_reranking,
            'supports_files' => $this->supports_files,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
