<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Contracts\UserModelInterface;
use App\Models\UserSetting;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Core\Database\Factories\AiProviderFactory;
use Modules\Core\Http\Resources\Api\V1\UserAiProviderResource;
use Modules\Core\Policies\AiProviderPolicy;
use Override;

/**
 * @property-read int $id
 * @property int $user_setting_id
 * @property int $user_id
 * @property string $code
 * @property string $name
 * @property string $driver
 * @property bool $enabled
 * @property string $api_key
 * @property string|null $url
 * @property string|null $api_version
 * @property string|null $deployment
 * @property string|null $embedding_deployment
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read UserSetting $userSetting
 * @property-read Collection<int, AiModel> $models
 */
#[Table(name: 'core_ai_providers')]
#[UseFactory(AiProviderFactory::class)]
#[UsePolicy(AiProviderPolicy::class)]
#[UseResource(UserAiProviderResource::class)]
final class AiProvider extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;

    /**
     * @return BelongsTo<UserSetting, $this>
     */
    public function userSetting(): BelongsTo
    {
        return $this->belongsTo(UserSetting::class);
    }

    /**
     * @return HasMany<AiModel, $this>
     */
    public function models(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'enabled' => 'boolean',
        ];
    }
}
