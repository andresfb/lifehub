<?php

declare(strict_types=1);

namespace Modules\Dashboard\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Models\HomepageSection;

final class HomepageSectionObserver
{
    public function creating(HomepageSection $section): void
    {
        if (filled($section->order)) {
            return;
        }

        $section->order = HomepageSection::query()->max('order') + 1;
    }

    public function saved(HomepageSection $section): void
    {
        Cache::tags("homepage:{$section->user_id}")->flush();
    }
}
