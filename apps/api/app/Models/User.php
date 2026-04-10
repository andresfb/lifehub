<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleStatus;
use App\Enums\ModuleVisibility;
use App\Observers\UserObserver;
use App\Services\Modules\ModuleAccessService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[ObservedBy([UserObserver::class])]
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
final class User extends Authenticatable implements MustVerifyEmail
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

    public function userModules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'user_modules')
            ->withPivot(['enabled', 'access_level', 'visibility', 'settings', 'granted_by'])
            ->withTimestamps();
    }

    public function accessibleModules(): BelongsToMany
    {
        return $this->modules()
            ->wherePivot('enabled', true)
            ->wherePivot('visibility', ModuleVisibility::VISIBLE)
            ->wherePivotIn('access_level', [
                ModuleAccessLevel::READ,
                ModuleAccessLevel::WRITE,
                ModuleAccessLevel::ADMIN,
            ])
            ->where('status', ModuleStatus::ACTIVE);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(ModuleAccessService::SUPER_ADMIN_ROLE);
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
