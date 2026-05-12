<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Api\V1;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Modules\Core\Models\AiModel;

/**
 * @mixin AiModel
 */
final class UserAiModelResource extends JsonApiResource
{
    /** @var array<int, string> */
    public array $attributes = [
        'provider_id',
        'name',
        'enabled',
        'supports_text',
        'supports_images',
        'supports_tts',
        'supports_stt',
        'supports_embeddings',
        'supports_reranking',
        'created_at',
        'updated_at',
    ];
}
