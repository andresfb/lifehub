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
 * @property int $api_manifest_module_id
 * @property int|null $parent_id
 * @property string $node_id
 * @property string $key
 * @property string $name
 * @property string $web_path
 * @property string|null $icon
 * @property string|null $shortcut
 * @property bool $show
 * @property int $sort_order
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read ApiManifestModule $module
 * @property-read ApiManifestNavigationNode|null $parent
 * @property-read Collection<ApiManifestNavigationNode>|null $children
 */
#[Guarded('id')]
#[Table(name: 'api_manifest_navigation_node')]
final class ApiManifestNavigationNode extends Model
{
    use HasFactory;

    public function module(): BelongsTo
    {
        return $this->belongsTo(ApiManifestModule::class, 'api_manifest_module_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected function casts(): array
    {
        return [
            'show' => 'boolean',
        ];
    }
}
