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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property string $user_id
 * @property string $version
 * @property array $payload
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read Collection<ApiManifestModule> $modules
 * @property-read Collection<ApiManifestEndpoint>|null $endpoints
 */
#[Guarded('id')]
#[Table(name: 'api_manifest')]
final class ApiManifest extends Model
{
    use HasFactory;

    public static function getUserNavigation(int $userId): ?self
    {
        return self::query()
            ->withNavigation()
            ->where('user_id', $userId)
            ->first();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ApiManifestModule::class);
    }

    public function endpoints(): HasMany
    {
        return $this->hasMany(ApiManifestEndpoint::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function modulesPayload(): array
    {
        $payload = $this->payload;

        if (isset($payload['data']['modules']) && is_array($payload['data']['modules'])) {
            return $payload['data']['modules'];
        }

        if (isset($payload[0]) && is_array($payload[0])) {
            return $payload;
        }

        return [];
    }

    #[Scope]
    protected function withNavigation(Builder $query): void
    {
        $query->with([
            'modules' => fn ($modules) => $modules
                ->orderBy('sort_order')
                ->with([
                    'navigation' => fn ($navigation) => $navigation
                        ->whereHas('children')
                        ->whereNull('parent_id')
                        ->orderBy('sort_order')
                        ->with([
                            'children' => fn ($children) => $children
                                ->orderBy('sort_order'),
                        ]),
                ]),
        ]);
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
