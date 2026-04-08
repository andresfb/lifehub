<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Observers;

use App\Domain\Dashboard\Models\HomepageSection;
use Illuminate\Support\Facades\Cache;

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
