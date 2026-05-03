<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Modules\Dashboard\Models\HomepageSection;

final class PinsAction
{
    /**
     * @return Collection<int, HomepageSection>
     */
    public function handle(int $userId, int $status): Collection
    {
        return HomepageSection::getUserSections($userId, $status);
    }
}
