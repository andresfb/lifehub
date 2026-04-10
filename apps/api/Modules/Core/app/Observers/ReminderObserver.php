<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use App\Services\Search\SearchDocumentProjector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Core\Models\Reminder;

final class ReminderObserver implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly SearchDocumentProjector $projector
    ) {}

    public function saved(Reminder $reminder): void
    {
        $this->projector->upsert(
            $reminder->buildGlobalSearch(),
            $reminder->user_id,
        );
    }

    public function deleted(Reminder $reminder): void
    {
        $this->projector->remove($reminder->getIdentifier());
    }
}
