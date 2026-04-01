<?php

namespace App\Observers;

use App\Models\Reminder;
use App\Service\Search\SearchDocumentProjector;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReminderObserver implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly SearchDocumentProjector $projector
    ) {}

    public function saved(Reminder $reminder): void
    {
        $this->projector->upsert(
            $reminder->buildGlobalSearch(),
        );
    }

    public function deleted(Reminder $reminder): void
    {
        $this->projector->remove($reminder->getIdentifier());
    }
}
