<?php

declare(strict_types=1);

namespace Modules\Dashboard\Observers;

use Modules\Dashboard\Models\HomepageSection;
use Spatie\ResponseCache\Facades\ResponseCache;

final class HomepageSectionObserver
{
    public function creating(HomepageSection $section): void
    {
        if (filled($section->order)) {
            return;
        }

        $section->order = HomepageSection::query()->max('order') + 1;
    }

    public function saved(): void
    {
        ResponseCache::clear(['dashboard']);
    }
}
