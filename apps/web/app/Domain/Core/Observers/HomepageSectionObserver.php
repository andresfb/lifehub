<?php

declare(strict_types=1);

namespace App\Domain\Core\Observers;

use App\Domain\Core\Models\HomepageSection;
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

    public function saved(): void
    {
        Cache::tags('homepage')->flush();
    }
}
