<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Models\Tag;
use App\Models\User;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Enums\MorphTypes;
use Modules\Core\Observers\ReminderObserver;
use Override;
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
 */
#[ObservedBy([ReminderObserver::class])]
#[Table(name: 'core_reminders')]
final class Reminder extends Model implements GlobalSearchInterface, UserModelInterface
{
    use BelongsToUser;
    use CascadeSoftDeletes;
    use HasFactory;
    use HasTags;
    use SoftDeletes;

    protected array $cascadeDeletes = ['tags'];

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
            'module' => 'CORE',
            'title' => $this->title,
            'body' => $this->notes ?? '',
            'tags' => $this->tags?->pluck('name')->values()->all() ?? [],
            'keywords' => [],
            'metadata' => [
                'icon' => 'note',
            ],
            'urls' => [
                'api' => 'api.v1.reminder.show',
            ],
            'is_private' => false,
            'is_archived' => filled($this->deleted_at),
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
