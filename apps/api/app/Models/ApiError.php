<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $source_id
 * @property string $type
 * @property string $error
 * @property array $data
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class ApiError extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
