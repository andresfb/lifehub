<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Enums\MorphTypes;
use App\Observers\ReminderObserver;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $remindable_id
 * @property-read string $remindable_type
 * @property string $title
 * @property string|null $notes
 * @property CarbonImmutable $due_at
 * @property CarbonImmutable|null $completed_at
 * @property CarbonImmutable|null $snoozed_until
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Collection<Tag> $tags
 * @property-read Collection<Audit> $audits
 */
#[ObservedBy([ReminderObserver::class])]
final class Reminder extends Model implements Auditable, GlobalSearchInterface, UserModelInterface
{
    use AuditableTrait;
    use BelongsToUser;
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected array $auditExclude = [
        'tags', 'audits',
    ];

    public function getIdentifier(): string
    {
        return "reminder:{$this->id}";
    }

    public function buildGlobalSearch(): array
    {
        return [
            'id' => $this->getIdentifier(),
            'user_id' => (string) $this->user_id,
            'entity_type' => MorphTypes::CORE_REMINDER->name,
            'entity_id' => (string) $this->id,
            'module' => 'core',
            'title' => $this->title,
            'body' => $this->notes ?? '',
            'tags' => $this->tags?->pluck('name')->values()->all() ?? [],
            'keywords' => [],
            'metadata' => [
                'icon' => 'note',
            ],
            'urls' => [
                'web' => route('reminder.show', $this),
                'api' => route('api.v1.reminder.show', $this),
            ],
            'is_private' => true,
            'is_archived' => false,
            'source_updated_at' => $this->updated_at,
        ];
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'due_at' => 'timestamp',
            'completed_at' => 'timestamp',
            'snoozed_until' => 'timestamp',
        ];
    }
}
