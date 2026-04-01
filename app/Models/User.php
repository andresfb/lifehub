<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\AdminHashable;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Override;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property string|null $admin_hash
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'admin_hash'])]
final class User extends Authenticatable implements MustVerifyEmail
{
    use AdminHashable;
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    public function userModules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(AppModule::class, 'user_modules')
            ->withPivot(['enabled', 'access_level', 'visibility', 'settings', 'granted_by'])
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return Cache::remember(
            md5("USER:ADMIN_HASH:{$this->id}"),
            now()->addMonth(),
            function (): bool {
                return $this->isHashValid($this->id, $this->admin_hash);
            }
        );
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
