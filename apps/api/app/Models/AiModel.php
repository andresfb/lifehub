<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Http\Resources\UserAiModelResource;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read int $id
 * @property-read int $ai_provider_id
 * @property int $user_id
 * @property string $name
 * @property bool $enabled
 * @property bool $supports_text
 * @property bool $supports_images
 * @property bool $supports_tts
 * @property bool $supports_stt
 * @property bool $supports_embeddings
 * @property bool $supports_reranking
 * @property bool $supports_files
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read AiProvider $provider
 */
#[UseFactory(AiModelFactory::class)]
#[UseResource(UserAiModelResource::class)]
final class AiModel extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;

    /**
     * @return BelongsTo<AiProvider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'supports_text' => 'boolean',
            'supports_images' => 'boolean',
            'supports_tts' => 'boolean',
            'supports_stt' => 'boolean',
            'supports_embeddings' => 'boolean',
            'supports_reranking' => 'boolean',
            'supports_files' => 'boolean',
        ];
    }
}
