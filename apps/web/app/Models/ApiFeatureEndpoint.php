<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $api_feature_id
 * @property string $route_name
 * @property string $method
 * @property string $path
 * @property string $operation_id
 * @property int $sort_order
 */
class ApiFeatureEndpoint extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(ApiFeature::class, 'api_feature_id');
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
