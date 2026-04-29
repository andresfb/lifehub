<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $api_manifest_module_id
 * @property int $api_manifest_endpoint_id
 * @property string $owner
 * @property string $code
 * @property string $name
 * @property string $required_access
 * @property string|null $shortcut
 * @property int $sort_order
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read ApiManifestModule $module
 * @property-read ApiManifestEndpoint $endpoint
 */
#[Guarded('id')]
#[Table(name: 'api_manifest_command')]
final class ApiManifestCommand extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<ApiManifestModule, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(ApiManifestModule::class, 'api_manifest_module_id');
    }

    /**
     * @return BelongsTo<ApiManifestEndpoint, $this>
     */
    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(ApiManifestEndpoint::class, 'api_manifest_endpoint_id');
    }
}
