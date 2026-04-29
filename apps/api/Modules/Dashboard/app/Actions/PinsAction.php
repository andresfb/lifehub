<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionCollection;
use Modules\Dashboard\Models\HomepageSection;

final class PinsAction
{
    public function handle(int $userId): HomepageSectionCollection
    {
        return new HomepageSectionCollection(
            HomepageSection::getUserSections($userId)
        );
    }
}
