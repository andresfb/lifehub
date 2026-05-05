<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Enums;

enum ManifestActionOwner: string
{
    case AI_PROVIDER = 'ai.providers';
    case AI_MODEL = 'ai.models';
    case SEARCH_TAGS = 'search.tags';
    case PINS = 'dashboard.pins';
    case SEARCH = 'dashboard.search';
}
