<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property int $api_manifest_id
 * @property string $key
 * @property string $name
 * @property string $description
 * @property bool $is_public
 * @property int $sort_order
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read ApiManifest $manifest
 * @property-read Collection<int, ApiManifestNavigationNode>|null $navigationNodes
 * @property-read Collection<int, ApiManifestCommand>|null $commands
 * @property-read Collection<int, ApiManifestAction>|null $actions
 */
#[Guarded('id')]
#[Table(name: 'api_manifest_module')]
final class ApiManifestModule extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<ApiManifest, $this>
     */
    public function manifest(): BelongsTo
    {
        return $this->belongsTo(ApiManifest::class, 'api_manifest_id');
    }

    /**
     * @return HasMany<ApiManifestNavigationNode, $this>
     */
    public function navigation(): HasMany
    {
        return $this->hasMany(ApiManifestNavigationNode::class, 'api_manifest_module_id');
    }

    /**
     * @return HasMany<ApiManifestCommand, $this>
     */
    public function commands(): HasMany
    {
        return $this->hasMany(ApiManifestCommand::class, 'api_manifest_module_id');
    }

    /**
     * @return HasMany<ApiManifestAction, $this>
     */
    public function actions(): HasMany
    {
        return $this->hasMany(ApiManifestAction::class, 'api_manifest_module_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
