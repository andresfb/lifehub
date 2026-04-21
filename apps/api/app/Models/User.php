<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\UserObserver;
use App\Services\Modules\ModuleAccessService;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Models\AiModel;
use Modules\Core\Models\AiProvider;
use Modules\Core\Services\AI\UserAiSettingsService;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property string $two_factor_secret
 * @property string $two_factor_recovery_codes
 * @property-read CarbonImmutable|null $two_factor_confirmed_at
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read UserSetting $aiSettings
 * @property-read Collection<AiProvider> $aiProviders
 * @property-read Collection<AiModel> $aiModels
 */
#[ObservedBy([UserObserver::class])]
#[UseFactory(UserFactory::class)]
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
final class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    public static function getAdmin(): self
    {
        return self::query()
            ->role(ModuleAccessService::SUPER_ADMIN_ROLE)
            ->firstOrFail();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(ModuleAccessService::SUPER_ADMIN_ROLE);
    }

    /**
     * @return HasOne<UserSetting, $this>
     */
    public function aiSettings(): HasOne
    {
        return $this->hasOne(UserSetting::class)
            ->where('key', UserSetting::AI_KEY);
    }

    /**
     * @return HasMany<AiProvider, $this>
     */
    public function aiProviders(): HasMany
    {
        return $this->hasMany(AiProvider::class);
    }

    /**
     * @return HasMany<AiModel, $this>
     */
    public function aiModels(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function ensureAiSettings(): UserSetting
    {
        return resolve(UserAiSettingsService::class)->ensureRootSetting($this);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
