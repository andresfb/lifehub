<?php

namespace App\Repository\Auth\Enums;

enum LoginStatus: string
{
    case SUCCESS = 'success';
    case FAILURE = 'failure';
    case TWO_FACTOR = 'two-factor';
}
