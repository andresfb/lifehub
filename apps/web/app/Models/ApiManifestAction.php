<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $api_manifest_module_id
 * @property int $api_manifest_endpoint_id
 * @property string $owner
 * @property string $name
 * @property string $required_access
 * @property int $sort_order
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read ApiManifestModule $module
 * @property-read ApiManifestEndpoint $endpoint
 */
#[Table(name: 'api_manifest_action')]
final class ApiManifestAction extends Model
{
    protected $fillable = [
        'api_manifest_module_id',
        'api_manifest_endpoint_id',
        'owner',
        'name',
        'required_access',
        'sort_order',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ApiManifestModule::class, 'api_manifest_module_id');
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiManifestEndpoint::class, 'api_manifest_endpoint_id');
    }
}
