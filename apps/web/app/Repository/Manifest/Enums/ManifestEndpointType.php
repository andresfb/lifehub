<?php

namespace App\Repository\Manifest\Enums;

enum ManifestEndpointType: string
{
    case COMMAND = 'command';
    case ACTION = 'action';
}
