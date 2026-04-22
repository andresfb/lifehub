<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $api_feature_id
 * @property string $web_path
 * @property string $tui_command
 * @property string $icon
 * @property string $shortcut_key
 * @property bool $show_in_menu
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 */
class ApiFeatureNav extends Model
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
