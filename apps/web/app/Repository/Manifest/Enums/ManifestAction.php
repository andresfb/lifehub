<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Enums;

enum ManifestAction: string
{
    case LIST = 'list';
    case SHOW = 'show';
    case SAVE = 'save';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case SEARCH = 'search';
}
