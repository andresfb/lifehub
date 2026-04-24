<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $version
 * @property array $payload
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[Table(name: 'api_manifest')]
final class ApiManifest extends Model
{
    use HasFactory;

    public static function getForUser(int $userId): ?self
    {
        return self::query()
            ->where('user_id', $userId)
            ->first();
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
