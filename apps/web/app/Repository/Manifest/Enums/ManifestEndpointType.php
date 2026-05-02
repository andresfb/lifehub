<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Enums;

enum ManifestEndpointType: string
{
    case COMMAND = 'command';
    case ACTION = 'action';
}
