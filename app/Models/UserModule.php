<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleVisibility;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read int $id
 * @property-read int $module_id
 * @property-read int $user_id
 * @property-read int $granted_by
 * @property bool $enabled
 * @property ModuleAccessLevel $access_level
 * @property ModuleVisibility $visibility
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read AppModule $module
 * @property-read User $user
 * @property-read User $grantedBy
 */
final class UserModule extends Model
{
    use HasFactory;

    public function module(): BelongsTo
    {
        return $this->belongsTo(AppModule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'settings' => 'array',
            'access_level' => ModuleAccessLevel::class,
            'visibility' => ModuleVisibility::class,
        ];
    }
}
