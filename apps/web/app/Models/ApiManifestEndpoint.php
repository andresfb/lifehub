<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property int $api_manifest_id
 * @property string $route_name
 * @property string $method
 * @property string $path
 * @property string $operation_id
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read ApiManifest $manifest
 * @property-read Collection<ApiManifestCommand> $commands
 * @property-read Collection<ApiManifestAction> $actions
 */
#[Table(name: 'api_manifest_endpoint')]
final class ApiManifestEndpoint extends Model
{
    protected $fillable = [
        'api_manifest_id',
        'route_name',
        'method',
        'path',
        'operation_id',
    ];

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(ApiManifest::class, 'api_manifest_id');
    }

    public function commands(): HasMany
    {
        return $this->hasMany(ApiManifestCommand::class, 'api_manifest_endpoint_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ApiManifestAction::class, 'api_manifest_endpoint_id');
    }
}
