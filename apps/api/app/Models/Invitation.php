<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * @property-read int $id
 * @property string $email
 * @property string $token
 * @property CarbonImmutable|null $accepted_at
 * @property CarbonImmutable $expires_at
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[UseFactory(InvitationFactory::class)]
final class Invitation extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @param Builder<self> $query */
    protected function scopeValid(Builder $query): void
    {
        $query->whereNull('accepted_at')
            ->where('expires_at', '>', now());
    }

    /** @param Builder<self> $query */
    protected function scopeForToken(Builder $query, string $token): void
    {
        $query->where('token', $token);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
