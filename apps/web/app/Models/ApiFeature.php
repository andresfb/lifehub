<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property int $api_module_id
 * @property int $parent_id
 * @property string $external_id
 * @property string $title
 * @property string $kind
 * @property string $required_access
 * @property int $sort_order
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 */
class ApiFeature extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ApiModule::class, 'api_module_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id')
            ->orderBy('sort_order');
    }

    public function nav(): HasOne
    {
        return $this->hasOne(ApiFeatureNav::class);
    }

    public function endpoints(): HasMany
    {
        return $this->hasMany(ApiFeatureEndpoint::class)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
