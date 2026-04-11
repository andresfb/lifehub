<?php

declare(strict_types=1);

namespace App\Enums;

enum FeatureKind: string
{
    case Screen = 'screen';
    case Action = 'action';
    case Group = 'group';
}
