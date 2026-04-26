<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionCollection;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionResource;
use Modules\Dashboard\Models\HomepageSection;
use Throwable;

final class PinsAction
{
    /**
     * @return Collection<HomepageSection>
     *
     * @throws Throwable
     */
    public function handle(int $userId): HomepageSectionCollection
    {
        return new HomepageSectionCollection(
            HomepageSection::getUserSections($userId)
        );
    }
}
