<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Enums;

enum ManifestModule: string
{
    case CORE = 'core';
    case DASHBOARD = 'dashboard';
}
