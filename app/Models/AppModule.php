<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModuleKey;
use App\Enums\ModuleStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * @property-read int $id
 * @property ModuleKey $key
 * @property string $name
 * @property string $description
 * @property bool $is_core
 * @property bool $is_public
 * @property ModuleStatus $status
 * @property array $settings_schema
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
final class AppModule extends Model
{
    use HasFactory;

    public function userModules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_modules')
            ->withPivot(['enabled', 'access_level', 'visibility', 'settings', 'granted_by'])
            ->withTimestamps();
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'is_public' => 'boolean',
            'settings_schema' => 'array',
            'key' => ModuleKey::class,
            'status' => ModuleStatus::class,
        ];
    }
}
