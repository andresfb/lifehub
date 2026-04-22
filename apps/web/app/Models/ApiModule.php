<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int $api_catalog_id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property bool $is_public
 * @property int $sort_order
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 */
class ApiModule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ApiCatalog::class, 'api_catalog_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ApiFeature::class)
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    public function allFeatures(): HasMany
    {
        return $this->hasMany(ApiFeature::class)
            ->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
