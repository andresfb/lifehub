<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
#[Guarded('id')]
#[Table(name: 'api_manifest_endpoint')]
final class ApiManifestEndpoint extends Model
{
    use HasFactory;

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

    #[Scope]
    protected function ofAction(Builder $query, int $userId, string $method, string $actionName, string $moduleKey): void
    {
        $query->select('api_manifest_endpoint.*')
            ->join('api_manifest_action', 'api_manifest_action.api_manifest_endpoint_id', '=', 'api_manifest_endpoint.id')
            ->join('api_manifest_module', 'api_manifest_module.id', '=', 'api_manifest_action.api_manifest_module_id')
            ->join('api_manifest', 'api_manifest.id', '=', 'api_manifest_module.api_manifest_id')
            ->where('api_manifest.user_id', $userId)
            ->where('api_manifest_module.key', $moduleKey)
            ->where('api_manifest_action.name', $actionName)
            ->where('api_manifest_endpoint.method', $method);
    }
}
