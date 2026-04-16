<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\UserSettingFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Override;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $key
 * @property array<string, mixed>|null $payload
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read Collection<AiProvider> $aiProviders
 */
#[UseFactory(UserSettingFactory::class)]
final class UserSetting extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;

    public const string AI_KEY = 'ai';

    /**
     * @return HasMany<AiProvider, $this>
     */
    public function aiProviders(): HasMany
    {
        return $this->hasMany(AiProvider::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
