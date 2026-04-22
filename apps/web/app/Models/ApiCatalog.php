<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $version
 * @property string $raw_payload
 * @property-read CarbonInterface|null $created_at
 * @property-read CarbonInterface|null $updated_at
 */
class ApiCatalog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function modules(): HasMany
    {
        return $this->hasMany(ApiModule::class)
            ->orderBy('sort_order');
    }

    public static function getUserCatalog(int $userId): ?self
    {
//        return Cache::remember(
//            sprintf("api-catalog-user-%d", $userId),
//            now()->addHours(8),
//            static function () use ($userId): ?self {
                return self::query()
                    ->with('modules.features.parent')
                    ->with('modules.features.children')
                    ->with('modules.features.endpoints')
                    ->with('modules.features.endpoints')
                    ->where('user_id', $userId)
                    ->first();
//            }
//        );
    }

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
        ];
    }
}
