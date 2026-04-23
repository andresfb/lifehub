<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $token_hash
 * @property string $api_token
 * @property bool $two_factor_enabled
 * @property bool $is_admin
 * @property CarbonImmutable|null $email_verified_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'api_token'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public static function getToken(int|string|null $identifier): string
    {
        if (blank($identifier)) {
            return '';
        }

        $query = is_string($identifier)
            ? self::query()->where('token_hash', $identifier)
            : self::query()->where('id', $identifier);

        $user = $query->first();
        if (blank($user)) {
            return '';
        }

        return $user->api_token;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'api_token' => 'encrypted',
        ];
    }
}
